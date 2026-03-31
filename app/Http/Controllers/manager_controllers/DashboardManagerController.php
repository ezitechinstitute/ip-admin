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

        // 5️⃣ Total Interns (under this manager via intern_accounts.manager_id)
        $totalInterns = DB::table('intern_accounts')
            ->where('manager_id', $manager->manager_id)
            ->count();

        // 6️⃣ Active Interns (status = Active in intern_accounts)
        $activeInterns = DB::table('intern_accounts')
            ->where('manager_id', $manager->manager_id)
            ->where('int_status', 'Active')
            ->count();

        // 7️⃣ Pending Interviews (from intern_table if interview not completed)
        $pendingInterviews = DB::table('intern_table')
            ->where('status', 'Interview')
            ->whereNull('interview_completed_at')
            ->where(function($q) use ($allowedTechNames, $allowedInternTypes) {
                $q->whereIn('technology', $allowedTechNames)
                  ->whereIn(DB::raw('LOWER(intern_type)'), $allowedInternTypes);
            })
            ->count();

        // 8️⃣ Pending Tests Review (from intern_table if test not completed)
        $pendingTests = DB::table('intern_table')
            ->where('status', 'Test')
            ->where(function($q) {
                $q->where('test_status', '!=', 'completed')
                  ->orWhereNull('test_status');
            })
            ->whereNull('test_completed_at')
            ->where(function($q) use ($allowedTechNames, $allowedInternTypes) {
                $q->whereIn('technology', $allowedTechNames)
                  ->whereIn(DB::raw('LOWER(intern_type)'), $allowedInternTypes);
            })
            ->count();

        // 9️⃣ Ongoing Projects (not completed)
        // Note: intern_projects doesn't link to manager directly, counting all
        $ongoingProjects = DB::table('intern_projects')
            ->where('pstatus', '!=', 'Completed')
            ->count();

        // 🔟 Expired Projects (end_date in past, not completed)
        $expiredProjects = DB::table('intern_projects')
            ->whereRaw("(end_date IS NOT NULL AND STR_TO_DATE(end_date, '%Y-%m-%d') < CURDATE())")
            ->where('pstatus', '!=', 'Completed')
            ->count();

        // 1️⃣1️⃣ Monthly Revenue (current month from transactions)
        // Note: invoices table doesn't have manager_id, so getting all transactions
        $currentMonth = now()->startOfMonth();
        $monthlyRevenue = DB::table('transactions')
            ->whereBetween('created_at', [$currentMonth, now()])
            ->sum('amount') ?? 0;

        // 1️⃣2️⃣ Commission Earned (from manager_accounts - note: column is 'comission' with one 'm')
        $commissionEarned = DB::table('manager_accounts')
            ->where('manager_id', $manager->manager_id)
            ->value('comission') ?? 0;

        return view('pages.manager.dashboard.dashboard', compact(
            'manager', 'statusCounts', 'managerHours', 'totalInterns', 'activeInterns',
            'pendingInterviews', 'pendingTests', 'ongoingProjects', 'expiredProjects',
            'monthlyRevenue', 'commissionEarned'
        ));
    }
}
