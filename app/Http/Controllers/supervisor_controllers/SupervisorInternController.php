<?php

namespace App\Http\Controllers\supervisor_controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupervisorInternController extends Controller
{
    public function myInterns(Request $request)
    {
        $supervisorTechnology = session('manager_department');

        $interns = \Illuminate\Support\Facades\DB::table('intern_accounts')
            ->select(
                'int_id',
                'eti_id',
                'name',
                'email',
                'phone',
                'int_technology',
                'internship_type',
                'start_date',
                'int_status'
            )
            ->when($supervisorTechnology, function ($query, $supervisorTechnology) {
                $query->where('int_technology', $supervisorTechnology);
            })
            ->limit(20)
            ->get();

        return view('content.supervisor.my-interns', compact('interns', 'supervisorTechnology'));
    }

    public function active()
    {
        $supervisorTechnology = session('manager_department');

        $interns = \Illuminate\Support\Facades\DB::table('intern_accounts')
            ->select(
                'intern_accounts.int_id',
                'intern_accounts.eti_id',
                'intern_accounts.name',
                'intern_accounts.email',
                'intern_accounts.int_technology',
                'intern_accounts.internship_type',
                'intern_accounts.start_date',
                'intern_accounts.int_status'
            )
            ->where('int_status', 'Active')
            ->when($supervisorTechnology, function ($query, $supervisorTechnology) {
                $query->where('int_technology', $supervisorTechnology);
            })
            ->limit(20)
            ->get();

        foreach ($interns as $intern) {
            $totalTasks = \Illuminate\Support\Facades\DB::table('intern_tasks')
                ->where('eti_id', $intern->eti_id)
                ->count();

            $completedTasks = \Illuminate\Support\Facades\DB::table('intern_tasks')
                ->where('eti_id', $intern->eti_id)
                ->where('task_status', 'Completed')
                ->count();

            $intern->progress = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
            $intern->total_tasks = $totalTasks;
            $intern->completed_tasks = $completedTasks;
        }

        return view('content.supervisor.active-interns', compact('interns', 'supervisorTechnology'));
    }

    public function contactWith()
    {
        $supervisorTechnology = session('manager_department');

        $interns = \Illuminate\Support\Facades\DB::table('intern_accounts')
            ->select(
                'int_id',
                'name',
                'email',
                'phone',
                'int_technology',
                'internship_type',
                'start_date',
                'int_status'
            )
            ->where('int_status', 'Contact')
            ->when($supervisorTechnology, function ($query, $supervisorTechnology) {
                $query->where('int_technology', $supervisorTechnology);
            })
            ->limit(20)
            ->get();

        return view('content.supervisor.contact-interns', compact('interns', 'supervisorTechnology'));
    }

    public function test()
    {
        $supervisorTechnology = session('manager_department');

        $interns = \Illuminate\Support\Facades\DB::table('intern_accounts')
            ->select(
                'int_id',
                'name',
                'email',
                'phone',
                'int_technology',
                'internship_type',
                'start_date',
                'int_status'
            )
            ->where('int_status', 'Test')
            ->when($supervisorTechnology, function ($query, $supervisorTechnology) {
                $query->where('int_technology', $supervisorTechnology);
            })
            ->limit(20)
            ->get();

        return view('content.supervisor.test-interns', compact('interns', 'supervisorTechnology'));
    }

    public function completed()
    {
        $supervisorTechnology = session('manager_department');

        $interns = \Illuminate\Support\Facades\DB::table('intern_accounts')
            ->select(
                'int_id',
                'name',
                'email',
                'phone',
                'int_technology',
                'internship_type',
                'start_date',
                'int_status'
            )
            ->where('int_status', 'Completed')
            ->when($supervisorTechnology, function ($query, $supervisorTechnology) {
                $query->where('int_technology', $supervisorTechnology);
            })
            ->limit(20)
            ->get();

        return view('content.supervisor.completed-interns', compact('interns', 'supervisorTechnology'));
    }

    public function newInterns()
    {
        $supervisorTechnology = session('manager_department');

        $interns = \Illuminate\Support\Facades\DB::table('intern_accounts')
            ->select(
                'int_id',
                'name',
                'email',
                'phone',
                'int_technology',
                'internship_type',
                'start_date',
                'int_status'
            )
            ->where('int_status', 'New')
            ->when($supervisorTechnology, function ($query, $supervisorTechnology) {
                $query->where('int_technology', $supervisorTechnology);
            })
            ->limit(20)
            ->get();

        return view('content.supervisor.new-interns', compact('interns', 'supervisorTechnology'));
    }

    public function show($id)
    {
        $supervisorTechnology = session('manager_department');

        $intern = \Illuminate\Support\Facades\DB::table('intern_accounts')
            ->where('int_id', $id)
            ->when($supervisorTechnology, function ($query, $supervisorTechnology) {
                $query->where('int_technology', $supervisorTechnology);
            })
            ->first();

        if (!$intern) {
            return redirect()->route('supervisor.myInterns')->with('error', 'Intern not found or not assigned to your technology.');
        }

        $tasks = \Illuminate\Support\Facades\DB::table('intern_tasks')
            ->where('eti_id', $intern->eti_id)
            ->orderByDesc('updated_at')
            ->get();

        $projects = \Illuminate\Support\Facades\DB::table('intern_projects')
            ->where('eti_id', $intern->eti_id)
            ->orderByDesc('updatedat')
            ->get();

        $evaluations = \Illuminate\Support\Facades\DB::table('intern_evaluations')
            ->where('eti_id', $intern->eti_id)
            ->orderByDesc('month')
            ->get();

        return view('content.supervisor.view-intern', compact('intern', 'tasks', 'projects', 'evaluations'));
    }

    public function progressMonitoring()
    {
        $supervisorTechnology = session('manager_department');

        $interns = \Illuminate\Support\Facades\DB::table('intern_accounts')
            ->where('int_status', 'Active')
            ->when($supervisorTechnology, function ($query, $supervisorTechnology) {
                $query->where('int_technology', $supervisorTechnology);
            })
            ->get();

        foreach ($interns as $intern) {
            $tasks = \Illuminate\Support\Facades\DB::table('intern_tasks')
                ->where('eti_id', $intern->eti_id)
                ->get();

            $total = $tasks->count();
            $completed = $tasks->where('task_status', 'Completed')->count();
            $expired = $tasks->where('task_status', 'Expired')->count();
            $overdue = $tasks->where('task_status', 'Assigned')
                ->where('task_end', '<', now()->toDateString())
                ->count();

            $intern->total_tasks = $total;
            $intern->completed_tasks = $completed;
            $intern->expired_tasks = $expired;
            $intern->overdue_tasks = $overdue;
            $intern->progress = $total > 0 ? round(($completed / $total) * 100) : 0;

            // Deadline compliance
            $intern->compliance = $total > 0 ? round(($completed / $total) * 100) : 100;
        }

        return view('content.supervisor.progress-monitoring', compact('interns', 'supervisorTechnology'));
    }
}