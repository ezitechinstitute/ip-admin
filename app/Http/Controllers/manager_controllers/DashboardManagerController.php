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


        // Fetch manager balance from manager_accounts table
        // $managerAccount = DB::table('manager_accounts')
        //     ->where('manager_id', $manager->manager_id)
        //     ->first();

        // $balance = $managerAccount->balance ?? 0;

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
        // $query = DB::table('intern_table')
        //     ->whereIn('status', ['Interview', 'Test', 'Contact', 'Completed', 'Active'])
        //     ->where(function($q) use ($allowedTechNames, $allowedInternTypes) {
        //         $q->whereIn('technology', $allowedTechNames)
        //           ->whereIn(DB::raw('LOWER(intern_type)'), $allowedInternTypes);
        //     });

        // dd($allowedTechNames, $allowedInternTypes);


        $query = DB::table('intern_table')
        ->whereIn('technology', $allowedTechNames)
        ->whereIn('intern_type', $allowedInternTypes);

        


        

        // 3️⃣ Get counts per status
        $statusCounts = $query->select('status', DB::raw('count(*) as total'))
                              ->groupBy('status')
                              ->pluck('total', 'status')
                              ->toArray();

        // Make sure all statuses exist
        // $allStatuses = ['Interview', 'Contact', 'Test', 'Completed'];
        $allStatuses = [
                'New',
                'Contact',
                'Test',
                'Completed',
                'Interview',
                'Selected',
                'Rejected'
            ];
        foreach ($allStatuses as $status) {
            if (!isset($statusCounts[$status])) $statusCounts[$status] = 0;
        }

        // 4️⃣ Add KPI Calculations UmairYaqoob Task
            $totalInterns = (clone $query)->count();

            $activeInterns = DB::table('intern_table')
                ->where('status', 'Active')
                ->whereIn('technology', $allowedTechNames)
                ->count();

            $pendingInterviews = $statusCounts['Interview'];
            $pendingTests = $statusCounts['Test'];
            
            //5️⃣ Project statistics
            $ongoingProjects = DB::table('intern_projects')
                ->where('pstatus', 'Ongoing')
                ->count();

            $expiredProjects = DB::table('intern_projects')
                ->where('pstatus', 'Expired')
                ->count();
            
            //6️⃣ Revenue calculation
            // Monthly Revenue (all payments received this month)

                $monthlyRevenue = DB::table('transactions')
                    ->whereMonth('created_at', now()->month)
                    ->sum('amount');


                // Manager Commission (already stored in database)

                $commission = DB::table('transactions')
                    ->whereMonth('created_at', now()->month)
                    ->sum('manager_amount');


                //Counts Active Interns (for this manager's allowed techs and types)
                $activeInterns = DB::table('intern_accounts')
                    ->where('int_status', 'Active')
                    ->count();

                // Counts Expired Projects (for this manager's allowed techs and types)
                $expiredProjects = DB::table('intern_projects')
                    ->where('pstatus', 'Expired')
                    ->count();



            //7️⃣ Interview Pipeline


        return view('pages.manager.dashboard.dashboard', compact(
            'manager',
            'statusCounts',
            'totalInterns',
            'activeInterns',
            'pendingInterviews',
            'pendingTests',
            'ongoingProjects',
            'expiredProjects',
            'monthlyRevenue',
            'commission'
        ));
    }
}
