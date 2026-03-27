<?php

namespace App\Http\Controllers\manager_controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardManagerController extends Controller
{
    public function index()
    {
        $manager = Auth::guard('manager')->user();
        if (!$manager) return redirect()->route('manager.login');

        // 1️⃣ Fetch manager permissions (tech + interview type)
        $allowedTechs = DB::table('manager_permissions')
            ->join('technologies', 'manager_permissions.tech_id', '=', 'technologies.tech_id')
            ->where('manager_permissions.manager_id', $manager->manager_id)
            ->where('technologies.status', 1)
            ->select('technologies.technology', 'manager_permissions.interview_type')
            ->get();

        $allowedTechNames = $allowedTechs->pluck('technology')->unique()->toArray();
        $allowedInternTypes = $allowedTechs->pluck('interview_type')
            ->map(fn($type) => strtolower(trim($type)))
            ->unique()
            ->toArray();

        // 2️⃣ Base query (all allowed interns for this manager)
        $query = DB::table('intern_table')
            ->whereIn('status', ['Interview', 'Test', 'Contact', 'Completed', 'Active'])
            ->where(function($q) use ($allowedTechNames, $allowedInternTypes) {
                $q->whereIn('technology', $allowedTechNames)
                  ->whereIn(DB::raw('LOWER(intern_type)'), $allowedInternTypes);
            });

        // 3️⃣ Get counts per status
        $statusCounts = $query->select('status', DB::raw('count(*) as total'))
                              ->groupBy('status')
                              ->pluck('total', 'status')
                              ->toArray();

        // Make sure all statuses exist
        $allStatuses = ['Interview', 'Contact', 'Test', 'Completed'];
        foreach ($allStatuses as $status) {
            if (!isset($statusCounts[$status])) $statusCounts[$status] = 0;
        }

        // ==================== KPI CARDS ====================
        
        // 4️⃣ Manager Shift Hours (from manager_accounts)
        $managerHours = DB::table('manager_accounts')
            ->where('manager_id', $manager->manager_id)
            ->value('shift_hours') ?? 0;

        // 5️⃣ Total Interns (under this manager)
        $totalInterns = DB::table('intern_table')
            ->where(function($q) use ($allowedTechNames, $allowedInternTypes) {
                $q->whereIn('technology', $allowedTechNames)
                  ->whereIn(DB::raw('LOWER(intern_type)'), $allowedInternTypes);
            })
            ->count();

        // 6️⃣ Active Interns
        $activeInterns = DB::table('intern_table')
            ->where('status', 'Active')
            ->where(function($q) use ($allowedTechNames, $allowedInternTypes) {
                $q->whereIn('technology', $allowedTechNames)
                  ->whereIn(DB::raw('LOWER(intern_type)'), $allowedInternTypes);
            })
            ->count();

        // 7️⃣ Pending Interviews (status = 'Interview' but not yet completed)
        $pendingInterviews = DB::table('intern_table')
            ->where('status', 'Interview')
            ->whereNull('interview_completed_at')
            ->where(function($q) use ($allowedTechNames, $allowedInternTypes) {
                $q->whereIn('technology', $allowedTechNames)
                  ->whereIn(DB::raw('LOWER(intern_type)'), $allowedInternTypes);
            })
            ->count();

        // 8️⃣ Pending Tests Review (status = 'Test' but not reviewed)
        $pendingTests = DB::table('intern_table')
            ->where('status', 'Test')
            ->where('test_status', '!=', 'completed')
            ->where(function($q) use ($allowedTechNames, $allowedInternTypes) {
                $q->whereIn('technology', $allowedTechNames)
                  ->whereIn(DB::raw('LOWER(intern_type)'), $allowedInternTypes);
            })
            ->count();

        // 9️⃣ Ongoing Projects (not completed, not expired)
        // Note: intern_projects table doesn't have manager_id field
        // Counting all ongoing projects across all supervisors
        $ongoingProjects = DB::table('intern_projects')
            ->where('pstatus', '!=', 'Completed')
            ->where(function($q) {
                $q->whereNull('end_date')
                  ->orWhereRaw("STR_TO_DATE(end_date, '%Y-%m-%d') > CURDATE()");
            })
            ->count();

        // 🔟 Expired Projects (end_date < now)
        $expiredProjects = DB::table('intern_projects')
            ->whereRaw("STR_TO_DATE(end_date, '%Y-%m-%d') < CURDATE()")
            ->where('pstatus', '!=', 'Completed')
            ->count();

        // 1️⃣1️⃣ Monthly Revenue (current month)
        $currentMonth = now()->startOfMonth();
        $monthlyRevenue = DB::table('transactions')
            ->join('invoices', 'transactions.invoice_id', '=', 'invoices.id')
            ->where('invoices.manager_id', $manager->manager_id)
            ->whereBetween('transactions.created_at', [$currentMonth, now()])
            ->sum('transactions.amount') ?? 0;

        // 1️⃣2️⃣ Commission Earned (current month)
        $commissionEarned = DB::table('manager_accounts')
            ->where('manager_id', $manager->manager_id)
            ->value('commission') ?? 0;

        return view('pages.manager.dashboard.dashboard', compact(
            'manager', 'statusCounts', 'managerHours', 'totalInterns', 'activeInterns',
            'pendingInterviews', 'pendingTests', 'ongoingProjects', 'expiredProjects',
            'monthlyRevenue', 'commissionEarned'
        ));
    }
}
