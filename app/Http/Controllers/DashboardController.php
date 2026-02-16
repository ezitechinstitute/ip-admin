<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Mail\BroadcastMail;
use App\Models\AdminAccount;
use App\Models\Intern;
use App\Models\InternAccount;
use App\Models\InternProject;
use App\Models\InternTask;
use App\Models\ProjectTask;
use App\Models\Technologies;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
 public function index()
    {
        $interviewCount = Intern::where('status', 'Interview')->count();
        $contactCount   = Intern::where('status', 'Contact')->count();
        $testCount      = Intern::where('status', 'Test')->count();
        $completedCount = Intern::where('status', 'Completed')->count();

        $totalInterns  = Intern::count();
        $activeInterns = Intern::where('status', 'active')->count();
        $totalProjects = InternProject::count();
        $totalTasks    = InternTask::count();

        $ongoing   = InternProject::where('pstatus', 'Ongoing')->count();
        $submitted = ProjectTask::where('task_status', 'Submitted')->count();
        $completed = InternProject::where('pstatus', 'Completed')->count();
        $expired   = InternProject::where('pstatus', 'Expired')->count();

        $internAC = InternAccount::count();
        $adminDetails = AdminAccount::first();

        $activeTechnologies = Technologies::where('status', 1)->get();

        
        $currentYear = date('Y');
        $years = range($currentYear - 4, $currentYear); // [2022, 2023, 2024, 2025, 2026]

      
        $monthlyRaw = Intern::select(
                DB::raw('MONTH(created_at) as month'),
                'interview_type',
                DB::raw('count(*) as count')
            )
            ->whereYear('created_at', $currentYear)
            ->groupBy('month', 'interview_type')
            ->get();

        $onsiteMonthlyArr = array_fill(1, 12, 0);
        $remoteMonthlyArr = array_fill(1, 12, 0);

        foreach ($monthlyRaw as $data) {
            if ($data->interview_type == 'Onsite') {
                $onsiteMonthlyArr[$data->month] = (int)$data->count;
            } else {
                $remoteMonthlyArr[$data->month] = (int)$data->count;
            }
        }
        
        // Convert to simple numerical arrays for JS
        $onsiteMonthly = array_values($onsiteMonthlyArr);
        $remoteMonthly = array_values($remoteMonthlyArr);

        // B. Quarterly Data (Calculated from Monthly)
        $onsiteQuarterly = [];
        $remoteQuarterly = [];
        for ($m = 1; $m <= 10; $m += 3) {
            $onsiteQuarterly[] = array_sum(array_slice($onsiteMonthlyArr, $m - 1, 3, true));
            $remoteQuarterly[] = array_sum(array_slice($remoteMonthlyArr, $m - 1, 3, true));
        }

        // C. Yearly Data (Last 5 Years)
        $yearlyRaw = Intern::select(
                DB::raw('YEAR(created_at) as year'),
                'interview_type',
                DB::raw('count(*) as count')
            )
            ->whereIn(DB::raw('YEAR(created_at)'), $years)
            ->groupBy('year', 'interview_type')
            ->get();

        $onsiteYearlyMap = array_fill_keys($years, 0);
        $remoteYearlyMap = array_fill_keys($years, 0);

        foreach ($yearlyRaw as $data) {
            if ($data->interview_type == 'Onsite') {
                $onsiteYearlyMap[$data->year] = (int)$data->count;
            } else {
                $remoteYearlyMap[$data->year] = (int)$data->count;
            }
        }

        // IMPORTANT: array_values() keys ko remove karke indexing 0,1,2,3,4 kar deta hai
        // Taake ApexCharts ke categories [2022, 2023...] ke sath perfect match ho.
        $onsiteYearly = array_values($onsiteYearlyMap);
        $remoteYearly = array_values($remoteYearlyMap);

        // D. Totals for Display
        $totalOnsite = array_sum($onsiteMonthly);
        $totalRemote = array_sum($remoteMonthly);

        // --- 3. RETURN VIEW ---
        return view('pages.admin.dashboard.dashboard', compact(
            'interviewCount', 'contactCount', 'testCount', 'completedCount', 
            'totalInterns', 'activeInterns', 'totalProjects', 'totalTasks', 
            'ongoing', 'submitted', 'completed', 'expired', 'internAC',
            'onsiteMonthly', 'remoteMonthly', 
            'onsiteQuarterly', 'remoteQuarterly',
            'onsiteYearly', 'remoteYearly', 'adminDetails',
            'years', 'totalOnsite', 'totalRemote', 'activeTechnologies'
        ));
    }



   public function sendTargetedBroadcast(Request $request)
{
    // English comments: Validate that the message is not empty before processing
    $request->validate([
        'message' => 'required',
    ]);

    try {
        $query = DB::table('intern_accounts');

        if ($request->int_status !== 'all') {
            $query->where('int_status', $request->int_status);
        }
        
        if ($request->int_technology !== 'all') {
            $query->where('int_technology', $request->int_technology);
        }

        $total = $query->count();

        if ($total == 0) {
            return back()->with('error', 'No interns found matching your criteria!');
        }

        // English comments: Chunking 25k records to prevent memory overflow
        $query->orderBy('int_id')->chunk(500, function ($interns) use ($request) {
            foreach ($interns as $intern) {
                // English comments: Adding to queue. If this fails, it catches in the block below.
                Mail::to($intern->email)->queue(new BroadcastMail($intern->name, $request->message));
            }
        });

        return back()->with('success', "Processing started! $total emails are being added to the queue successfully.");

    } catch (\Exception $e) {
        // English comments: If any error occurs during database query or queueing
        return back()->with('error', 'Something went wrong: ' . $e->getMessage());
    }
}
    
    
    
    }
