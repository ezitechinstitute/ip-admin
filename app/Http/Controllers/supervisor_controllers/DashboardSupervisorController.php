<?php

namespace App\Http\Controllers\supervisor_controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardSupervisorController extends Controller
{
    public function index()
{
    $supervisorId = \Illuminate\Support\Facades\Auth::guard('manager')->id() ?? session('manager_id');
    $supervisorTechnology = trim(session('manager_department'));
    $today = now()->toDateString();

    // KPI Cards
    $totalAssignedInterns = \Illuminate\Support\Facades\DB::table('intern_accounts')
        ->when($supervisorTechnology, function ($query, $supervisorTechnology) {
            $query->whereRaw('LOWER(int_technology) = ?', [strtolower($supervisorTechnology)]);
        })
        ->count();

    $activeInterns = \Illuminate\Support\Facades\DB::table('intern_accounts')
        ->whereRaw('LOWER(int_status) = ?', ['active'])
        ->when($supervisorTechnology, function ($query, $supervisorTechnology) {
            $query->whereRaw('LOWER(int_technology) = ?', [strtolower($supervisorTechnology)]);
        })
        ->count();

    // Selection Phase Counts (New)
    $interviewCount = \App\Models\Intern::whereRaw('LOWER(status) = ?', ['interview'])
        ->when($supervisorTechnology, function ($query, $supervisorTechnology) {
            $query->whereRaw('LOWER(technology) = ?', [strtolower($supervisorTechnology)]);
        })->count();
    
    $contactCount = \App\Models\Intern::whereRaw('LOWER(status) = ?', ['contact'])
        ->when($supervisorTechnology, function ($query, $supervisorTechnology) {
            $query->whereRaw('LOWER(technology) = ?', [strtolower($supervisorTechnology)]);
        })->count();

    $testCount = \App\Models\Intern::whereRaw('LOWER(status) = ?', ['test'])
        ->when($supervisorTechnology, function ($query, $supervisorTechnology) {
            $query->whereRaw('LOWER(technology) = ?', [strtolower($supervisorTechnology)]);
        })->count();

    $completedCount = \App\Models\Intern::whereRaw('LOWER(status) = ?', ['completed'])
        ->when($supervisorTechnology, function ($query, $supervisorTechnology) {
            $query->whereRaw('LOWER(technology) = ?', [strtolower($supervisorTechnology)]);
        })->count();

    $pendingTaskReviews = \Illuminate\Support\Facades\DB::table('intern_tasks')
        ->where('assigned_by', $supervisorId)
        ->whereNotNull('submit_description')
        ->where(function ($query) {
            $query->whereNull('task_approve')
                  ->orWhere('task_approve', 0);
        })
        ->count();

    $tasksCompletedToday = \Illuminate\Support\Facades\DB::table('intern_tasks')
        ->where('assigned_by', $supervisorId)
        ->whereRaw('LOWER(task_status) = ?', ['completed'])
        ->whereDate('updated_at', $today)
        ->count();

    $overdueTasks = \Illuminate\Support\Facades\DB::table('intern_tasks')
        ->where('assigned_by', $supervisorId)
        ->whereDate('task_end', '<', $today)
        ->whereRaw('LOWER(task_status) != ?', ['completed'])
        ->count();

    $totalProjectsAssigned = \Illuminate\Support\Facades\DB::table('intern_projects')
        ->where('assigned_by', $supervisorId)
        ->count();

    // Recent Activity
    $newInterns = \Illuminate\Support\Facades\DB::table('intern_accounts')
        ->select('name', 'email', 'int_technology', 'start_date', 'int_status')
        ->when($supervisorTechnology, function ($query, $supervisorTechnology) {
            $query->whereRaw('LOWER(int_technology) = ?', [strtolower($supervisorTechnology)]);
        })
        ->orderByDesc('int_id')
        ->limit(5)
        ->get();

    $taskSubmissions = \Illuminate\Support\Facades\DB::table('intern_tasks')
        ->select('eti_id', 'task_title', 'task_status', 'updated_at', 'submit_description')
        ->where('assigned_by', $supervisorId)
        ->whereNotNull('submit_description')
        ->orderByDesc('updated_at')
        ->limit(5)
        ->get();

    $activityLogs = collect();
    if (\Illuminate\Support\Facades\Schema::hasTable('supervisor_activity_logs')) {
        $activityLogs = \Illuminate\Support\Facades\DB::table('supervisor_activity_logs')
            ->where('supervisor_id', $supervisorId)
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();
    }

    $notifications = collect();
    if (\Illuminate\Support\Facades\Schema::hasTable('supervisor_notifications')) {
        $notifications = \Illuminate\Support\Facades\DB::table('supervisor_notifications')
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
}