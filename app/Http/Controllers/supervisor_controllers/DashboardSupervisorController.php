<?php

namespace App\Http\Controllers\supervisor_controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Intern;
use App\Models\SupervisorPermission;

class DashboardSupervisorController extends Controller
{
    public function index()
    {
        // ✅ Supervisor ID
        $supervisorId = Auth::guard('manager')->id() ?? session('manager_id');

        // 1. Grab the specific Tech IDs the supervisor is allowed to see
        $permissionTechIds = \App\Models\SupervisorPermission::where('manager_id', $supervisorId)
            ->pluck('tech_id')
            ->toArray();

        // 2. Convert those IDs into the actual String Names from your technologies table
        $allowedTechNames = DB::table('technologies')
            ->whereIn('tech_id', $permissionTechIds)
            ->pluck('technology') // e.g., ['Laravel', 'React']
            ->toArray();

        // 🔥 THE FIX: If the array is empty (no permissions), force a fake name 
        // so the database safely searches for something that doesn't exist, returning 0.
        if (empty($allowedTechNames)) {
            $allowedTechNames = ['___NO_ACCESS___']; 
        }

        $today = now()->toDateString();

        // ================= KPI CARDS =================
        // Notice how we use $allowedTechNames here!

        $totalAssignedInterns = DB::table('intern_accounts')
            ->whereIn('int_technology', $allowedTechNames)
            ->count();

        $activeInterns = DB::table('intern_accounts')
            ->where('int_status', 'active')
            ->whereIn('int_technology', $allowedTechNames)
            ->count();

        // ================= SELECTION PHASE =================
        
        $interviewCount = Intern::where('status', 'interview')
            ->whereIn('technology', $allowedTechNames)
            ->count();

        $contactCount = Intern::where('status', 'contact')
            ->whereIn('technology', $allowedTechNames)
            ->count();

        $testCount = Intern::where('status', 'test')
            ->whereIn('technology', $allowedTechNames)
            ->count();

        $completedCount = Intern::where('status', 'completed')
            ->whereIn('technology', $allowedTechNames)
            ->count();

        // ================= TASKS =================
        // Tasks are filtered by 'assigned_by' so they don't need the technology filter

        $pendingTaskReviews = DB::table('intern_tasks')
            ->where('assigned_by', $supervisorId)
            ->whereNotNull('submit_description')
            ->where(function ($query) {
                $query->whereNull('task_approve')
                    ->orWhere('task_approve', 0);
            })
            ->count();

        $tasksCompletedToday = DB::table('intern_tasks')
            ->where('assigned_by', $supervisorId)
            ->where('task_status', 'completed')
            ->whereDate('updated_at', $today)
            ->count();

        $overdueTasks = DB::table('intern_tasks')
            ->where('assigned_by', $supervisorId)
            ->whereDate('task_end', '<', $today)
            ->where('task_status', '!=', 'completed')
            ->count();

        $totalProjectsAssigned = DB::table('intern_projects')
            ->where('assigned_by', $supervisorId)
            ->count();

        // ================= RECENT DATA =================

        $newInterns = DB::table('intern_accounts')
            ->select('name', 'email', 'int_technology', 'start_date', 'int_status')
            ->whereIn('int_technology', $allowedTechNames)
            ->orderByDesc('int_id')
            ->limit(5)
            ->get();

        $taskSubmissions = DB::table('intern_tasks')
            ->select('eti_id', 'task_title', 'task_status', 'updated_at', 'submit_description')
            ->where('assigned_by', $supervisorId)
            ->whereNotNull('submit_description')
            ->orderByDesc('updated_at')
            ->limit(5)
            ->get();

        // ================= OPTIONAL TABLES =================

        $activityLogs = collect();
        if (\Illuminate\Support\Facades\Schema::hasTable('supervisor_activity_logs')) {
            $activityLogs = DB::table('supervisor_activity_logs')
                ->where('supervisor_id', $supervisorId)
                ->orderByDesc('created_at')
                ->limit(10)
                ->get();
        }

        $notifications = collect();
        if (\Illuminate\Support\Facades\Schema::hasTable('supervisor_notifications')) {
            $notifications = DB::table('supervisor_notifications')
                ->where('supervisor_id', $supervisorId)
                ->orderByDesc('created_at')
                ->limit(5)
                ->get();
        }

        return view('content.supervisor.dashboard', compact(
            'totalAssignedInterns',
            'activeInterns',
            'interviewCount',
            'contactCount',
            'testCount',
            'completedCount',
            'pendingTaskReviews',
            'tasksCompletedToday',
            'overdueTasks',
            'totalProjectsAssigned',
            'newInterns',
            'taskSubmissions',
            'activityLogs',
            'notifications'
        ));
    }
//     public function index()
//     {
//         // ✅ Supervisor ID
//         $supervisorId = Auth::guard('manager')->id() ?? session('manager_id');

//         // ✅ Department from session
//         $supervisorTechnology = trim(session('manager_department'));

//         // 🔥 Department → Technology Mapping
//         // $departmentMap = [
//         //     'Web Development' => ['Laravel', 'React', 'NodeJS'],
//         //     'AI' => ['Python'],
//         //     'Data Science' => ['Python'],
//         //     'Mobile Development' => ['Flutter', 'Android'],
//         // ];

//         // $technologies = $departmentMap[$supervisorTechnology] ?? [];
//         $permissionTechIds = SupervisorPermission::where('manager_id', $supervisorId)
//     ->pluck('tech_id')
//     ->toArray();

// $technologies = DB::table('technologies')
//     ->whereIn('tech_id', $permissionTechIds)
//     ->pluck('technology')
//     ->toArray();

//         $today = now()->toDateString();

//         // ================= KPI CARDS =================

//         $totalAssignedInterns = DB::table('intern_accounts')
//             ->when(!empty($technologies), function ($query) use ($technologies) {
//                 $query->whereIn('int_technology', $technologies);
//             })
//             ->count();

//         $activeInterns = DB::table('intern_accounts')
//             ->where('int_status', 'active')
//             ->when(!empty($technologies), function ($query) use ($technologies) {
//                 $query->whereIn('int_technology', $technologies);
//             })
//             ->count();

//         // ================= SELECTION PHASE =================

//         $interviewCount = Intern::where('status', 'interview')
//             ->when(!empty($technologies), function ($query) use ($technologies) {
//                 $query->whereIn('technology', $technologies);
//             })
//             ->count();

//         $contactCount = Intern::where('status', 'contact')
//             ->when(!empty($technologies), function ($query) use ($technologies) {
//                 $query->whereIn('technology', $technologies);
//             })
//             ->count();

//         $testCount = Intern::where('status', 'test')
//             ->when(!empty($technologies), function ($query) use ($technologies) {
//                 $query->whereIn('technology', $technologies);
//             })
//             ->count();

//         $completedCount = Intern::where('status', 'completed')
//             ->when(!empty($technologies), function ($query) use ($technologies) {
//                 $query->whereIn('technology', $technologies);
//             })
//             ->count();

//         // ================= TASKS =================

//         $pendingTaskReviews = DB::table('intern_tasks')
//             ->where('assigned_by', $supervisorId)
//             ->whereNotNull('submit_description')
//             ->where(function ($query) {
//                 $query->whereNull('task_approve')
//                       ->orWhere('task_approve', 0);
//             })
//             ->count();

//         $tasksCompletedToday = DB::table('intern_tasks')
//             ->where('assigned_by', $supervisorId)
//             ->where('task_status', 'completed')
//             ->whereDate('updated_at', $today)
//             ->count();

//         $overdueTasks = DB::table('intern_tasks')
//             ->where('assigned_by', $supervisorId)
//             ->whereDate('task_end', '<', $today)
//             ->where('task_status', '!=', 'completed')
//             ->count();

//         $totalProjectsAssigned = DB::table('intern_projects')
//             ->where('assigned_by', $supervisorId)
//             ->count();

//         // ================= RECENT DATA =================

//         $newInterns = DB::table('intern_accounts')
//             ->select('name', 'email', 'int_technology', 'start_date', 'int_status')
//             ->when(!empty($technologies), function ($query) use ($technologies) {
//                 $query->whereIn('int_technology', $technologies);
//             })
//             ->orderByDesc('int_id')
//             ->limit(5)
//             ->get();

//         $taskSubmissions = DB::table('intern_tasks')
//             ->select('eti_id', 'task_title', 'task_status', 'updated_at', 'submit_description')
//             ->where('assigned_by', $supervisorId)
//             ->whereNotNull('submit_description')
//             ->orderByDesc('updated_at')
//             ->limit(5)
//             ->get();

//         // ================= OPTIONAL TABLES =================

//         $activityLogs = collect();
//         if (\Illuminate\Support\Facades\Schema::hasTable('supervisor_activity_logs')) {
//             $activityLogs = DB::table('supervisor_activity_logs')
//                 ->where('supervisor_id', $supervisorId)
//                 ->orderByDesc('created_at')
//                 ->limit(10)
//                 ->get();
//         }

//         $notifications = collect();
//         if (\Illuminate\Support\Facades\Schema::hasTable('supervisor_notifications')) {
//             $notifications = DB::table('supervisor_notifications')
//                 ->where('supervisor_id', $supervisorId)
//                 ->orderByDesc('created_at')
//                 ->limit(5)
//                 ->get();
//         }

//         return view('content.supervisor.dashboard', compact(
//             'totalAssignedInterns',
//             'activeInterns',
//             'interviewCount',
//             'contactCount',
//             'testCount',
//             'completedCount',
//             'pendingTaskReviews',
//             'tasksCompletedToday',
//             'overdueTasks',
//             'totalProjectsAssigned',
//             'newInterns',
//             'taskSubmissions',
//             'activityLogs',
//             'notifications'
//         ));
//     }
}