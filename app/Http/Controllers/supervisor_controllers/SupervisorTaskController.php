<?php

namespace App\Http\Controllers\supervisor_controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use App\Models\InternAccount;
use App\Models\InternTask;


class SupervisorTaskController extends Controller
{
    public function index()
    {
        $supervisorId = \Illuminate\Support\Facades\Auth::guard('manager')->id() ?? session('manager_id');
        // $tasks = DB::table('intern_tasks')
        //     ->join('intern_accounts', 'intern_tasks.eti_id', '=', 'intern_accounts.eti_id')
        //     ->select('intern_tasks.*', 'intern_accounts.name as intern_name')
        //     ->where('assigned_by', $supervisorId)
        //     ->orderByDesc('task_id')
        //     ->paginate(15);
        // 🔥 Get allowed technologies
        $permissionTechIds = \App\Models\SupervisorPermission::where('manager_id', $supervisorId)
            ->pluck('tech_id')
            ->toArray();

        $technologies = DB::table('technologies')
            ->whereIn('tech_id', $permissionTechIds)
            ->pluck('technology')
            ->toArray();

        $tasks = DB::table('intern_tasks')
            ->join('intern_accounts', 'intern_tasks.eti_id', '=', 'intern_accounts.eti_id')
            ->select('intern_tasks.*', 'intern_accounts.name as intern_name')
            ->where('assigned_by', $supervisorId)
            ->when(!empty($technologies), function ($query) use ($technologies) {
                $query->whereIn('intern_accounts.int_technology', $technologies);
            })
            ->orderByDesc('task_id')
            ->paginate(15);

        return view('content.supervisor.tasks.index', compact('tasks'));
    }

    // public function create()
    // {
    //     $supervisorTechnology = trim(session('manager_department'));
    //     $interns = DB::table('intern_accounts')
    //         ->whereRaw('LOWER(int_status) = ?', ['active'])
    //         ->when($supervisorTechnology, function ($query, $supervisorTechnology) {
    //             $query->whereRaw('LOWER(int_technology) = ?', [strtolower($supervisorTechnology)]);
    //         })
    //         ->get();

    //     return view('content.supervisor.tasks.create', compact('interns'));
    // }
    public function create()
    {
        $supervisorId = \Illuminate\Support\Facades\Auth::guard('manager')->id() ?? session('manager_id');

        // 🔥 Get allowed technologies from permissions
        $permissionTechIds = \App\Models\SupervisorPermission::where('manager_id', $supervisorId)
            ->pluck('tech_id')
            ->toArray();

        $technologies = DB::table('technologies')
            ->whereIn('tech_id', $permissionTechIds)
            ->pluck('technology')
            ->toArray();

        // 🔥 Get only allowed interns
        $interns = DB::table('intern_accounts')
            ->where('int_status', 'active')
            ->when(!empty($technologies), function ($query) use ($technologies) {
                $query->whereIn('int_technology', $technologies);
            })
            ->get();

        return view('content.supervisor.tasks.create', compact('interns'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'eti_ids' => 'required|array',
            'task_title' => 'required|string|max:255',
            'task_description' => 'required|string',
            'task_start' => 'required|date',
            'task_end' => 'required|date|after_or_equal:task_start',
            'task_points' => 'required|numeric',
        ]);

        $supervisorId = \Illuminate\Support\Facades\Auth::guard('manager')->id() ?? session('manager_id');

        if (!$supervisorId) {
            return back()->with('error', 'Authentication error: Supervisor ID not found. Please re-login.');
        }
        
        $start = \Carbon\Carbon::parse($request->task_start);
        $end = \Carbon\Carbon::parse($request->task_end);
        $days = $start->diffInDays($end) + 1;

        // foreach ($request->eti_ids as $eti_id) {
        //     DB::table('intern_tasks')->insert([
        //         'eti_id' => $eti_id,
        //         'task_title' => $request->task_title,
        //         'task_description' => $request->task_description,
        //         'task_start' => $request->task_start,
        //         'task_end' => $request->task_end,
        //         'task_days' => $days,
        //         'task_duration' => $days,
        //         'task_points' => $request->task_points,
        //         'task_obt_points' => 0,
        //         'assigned_by' => $supervisorId,
        //         'task_status' => 'Assigned',
        //         'review' => '',
        //         'task_screenshot' => '',
        //         'task_live_url' => '',
        //         'task_git_url' => '',
        //         'submit_description' => '',
        //         'created_at' => now(),
        //         'updated_at' => now(),
        //     ]);
        // }
        DB::beginTransaction();

        try {
            // 🔥 Validate interns exist
            $validInterns = DB::table('intern_accounts')
                ->whereIn('eti_id', $request->eti_ids)
                ->pluck('eti_id')
                ->toArray();

            $start = \Carbon\Carbon::parse($request->task_start);
            $end = \Carbon\Carbon::parse($request->task_end);
            $days = $start->diffInDays($end) + 1;

            foreach ($validInterns as $eti_id) {
                DB::table('intern_tasks')->insert([
                    'eti_id' => $eti_id,
                    'task_title' => $request->task_title,
                    'task_description' => $request->task_description,
                    'task_start' => $request->task_start,
                    'task_end' => $request->task_end,
                    'task_days' => $days,
                    'task_duration' => $days,
                    'task_points' => $request->task_points,
                    'task_obt_points' => 0,
                    'assigned_by' => $supervisorId,
                    'task_status' => 'Assigned',
                    'review' => '',
                    'task_screenshot' => '',
                    'task_live_url' => '',
                    'task_git_url' => '',
                    'submit_description' => '',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Task assignment failed');
        }

            $this->logActivity('Assigned Task', "Task: '{$request->task_title}' assigned to Interns: " . implode(', ', $request->eti_ids));
            foreach ($request->eti_ids as $eti_id) {
                $this->notifyIntern($eti_id, 'New Task', "You have been assigned a new task: '{$request->task_title}'");
            }

            return redirect()->route('supervisor.tasks.index')->with('success', 'Tasks assigned successfully.');
    }

    public function review($id)
    {
        $supervisorId = \Illuminate\Support\Facades\Auth::guard('manager')->id() ?? session('manager_id');
        $task = DB::table('intern_tasks')
            ->join('intern_accounts', 'intern_tasks.eti_id', '=', 'intern_accounts.eti_id')
            ->select('intern_tasks.*', 'intern_accounts.name as intern_name')
            ->where('task_id', $id)
            ->where('assigned_by', $supervisorId)
            ->first();

        if (!$task) {
            return redirect()->route('supervisor.tasks.index')->with('error', 'Task not found.');
        }

        return view('content.supervisor.tasks.review', compact('task'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'task_approve' => 'required|in:1,2', // 1: Approve, 2: Reject
            'code_quality_score' => 'nullable|integer|min:1|max:10',
            'remarks' => 'nullable|string',
            'penalty_flag' => 'nullable|boolean',
            'task_obt_points' => 'nullable|numeric',
        ]);

        $supervisorId = \Illuminate\Support\Facades\Auth::guard('manager')->id() ?? session('manager_id');

        $status = ($request->task_approve == 1) ? 'Completed' : 'Rejected';

        DB::table('intern_tasks')
            ->where('task_id', $id)
            ->where('assigned_by', $supervisorId)
            ->update([
                'task_approve' => $request->task_approve,
                'task_status' => $status,
                'code_quality_score' => $request->code_quality_score,
                'remarks' => $request->remarks,
                'penalty_flag' => $request->penalty_flag ?? false,
                'task_obt_points' => $request->task_obt_points ?? 0,
                'updated_at' => now(),
            ]);

        $this->logActivity('Reviewed Task', "Task ID: {$id} marked as {$status}");
        
        // Notify intern about review
        $taskInfo = DB::table('intern_tasks')->where('task_id', $id)->first();
        if ($taskInfo) {
            $eti_id = $taskInfo->eti_id;
            $title = $taskInfo->task_title;
            $this->notifyIntern($eti_id, 'Task Reviewed', "Your task '{$title}' has been {$status}. Remarks: {$request->remarks}");
        }

        return redirect()->route('supervisor.tasks.index')->with('success', 'Task review submitted successfully.');
    }

    public function kanban()
    {
        $supervisorId = \Illuminate\Support\Facades\Auth::guard('manager')->id() ?? session('manager_id');
        
        // 1. Fetch Standalone Tasks (intern_tasks)
        $standaloneTasks = DB::table('intern_tasks')
            ->join('intern_accounts', 'intern_tasks.eti_id', '=', 'intern_accounts.eti_id')
            ->select(
                'intern_tasks.task_id as id',
                'intern_tasks.task_title as title',
                'intern_tasks.task_end as end_date',
                'intern_tasks.task_status as status',
                'intern_accounts.name as intern_name',
                DB::raw("'standalone' as type"),
                DB::raw("NULL as project_id") // Standalone tasks don't have a project_id
            )
            ->where('intern_tasks.assigned_by', $supervisorId);

        // 2. Fetch Project Tasks (project_tasks)
        $projectTasks = DB::table('project_tasks')
            ->join('intern_accounts', 'project_tasks.eti_id', '=', 'intern_accounts.eti_id')
            ->select(
                'project_tasks.task_id as id',
                'project_tasks.task_title as title',
                'project_tasks.t_end_date as end_date',
                'project_tasks.task_status as status',
                'intern_accounts.name as intern_name',
                DB::raw("'project' as type"),
                'project_tasks.project_id' // Keep the project_id so we can generate the edit route
            )
            ->where('project_tasks.assigned_by', $supervisorId);

        // 3. Merge them together using UNION
        $tasks = $standaloneTasks->union($projectTasks)->get();

        return view('content.supervisor.tasks.kanban', compact('tasks'));
    }

    // public function kanban()
    // {
    //     $supervisorId = \Illuminate\Support\Facades\Auth::guard('manager')->id() ?? session('manager_id');
    //     $tasks = DB::table('intern_tasks')
    //         ->join('intern_accounts', 'intern_tasks.eti_id', '=', 'intern_accounts.eti_id')
    //         ->select('intern_tasks.*', 'intern_accounts.name as intern_name')
    //         ->where('assigned_by', $supervisorId)
    //         ->get();

    //     return view('content.supervisor.tasks.kanban', compact('tasks'));
    // }

    public function edit($id)
    {
        $supervisorId = \Illuminate\Support\Facades\Auth::guard('manager')->id() ?? session('manager_id');
        $task = DB::table('intern_tasks')
            ->where('task_id', $id)
            ->where('assigned_by', $supervisorId)
            ->first();

        if (!$task) {
            return redirect()->route('supervisor.tasks.index')->with('error', 'Task not found.');
        }

        return view('content.supervisor.tasks.edit', compact('task'));
    }

    public function updateDetails(Request $request, $id)
    {
        $request->validate([
            'task_title' => 'required|string|max:255',
            'task_description' => 'required|string',
            'task_start' => 'required|date',
            'task_end' => 'required|date|after_or_equal:task_start',
            'task_points' => 'required|numeric',
            'task_status' => 'required|string'
        ]);

        $supervisorId = \Illuminate\Support\Facades\Auth::guard('manager')->id() ?? session('manager_id');

        $start = \Carbon\Carbon::parse($request->task_start);
        $end = \Carbon\Carbon::parse($request->task_end);
        $days = $start->diffInDays($end) + 1;

        DB::table('intern_tasks')
            ->where('task_id', $id)
            ->where('assigned_by', $supervisorId)
            ->update([
                'task_title' => $request->task_title,
                'task_description' => $request->task_description,
                'task_start' => $request->task_start,
                'task_end' => $request->task_end,
                'task_days' => $days,
                'task_duration' => $days,
                'task_points' => $request->task_points,
                'task_status' => $request->task_status,
                'updated_at' => now(),
            ]);

        $this->logActivity('Updated Task', "Modified Task ID: {$id}");

        return redirect()->route('supervisor.tasks.index')->with('success', 'Task details updated successfully.');
    }
    
    

    public function destroy($id)
    {
        $supervisorId = \Illuminate\Support\Facades\Auth::guard('manager')->id() ?? session('manager_id');
        
        $task = DB::table('intern_tasks')->where('task_id', $id)->where('assigned_by', $supervisorId)->first();
        if ($task) {
            DB::table('intern_tasks')->where('task_id', $id)->delete();
            $this->logActivity('Deleted Task', "Removed Task: '{$task->task_title}'");
            return redirect()->route('supervisor.tasks.index')->with('success', 'Task deleted successfully.');
        }

        return redirect()->route('supervisor.tasks.index')->with('error', 'Task not found.');
    }

    
    


    //  AJAX endpoint to update task status from Kanban board
    public function updateStatusAjax(Request $request)
    {
        $request->validate([
            'task_id' => 'required|integer',
            'type' => 'required|string|in:project,standalone',
            'status' => 'required|string'
        ]);

        $supervisorId = \Illuminate\Support\Facades\Auth::guard('manager')->id() ?? session('manager_id');

        try {
            if ($request->type === 'project') {
                DB::table('project_tasks')
                    ->where('task_id', $request->task_id)
                    ->where('assigned_by', $supervisorId)
                    ->update(['task_status' => $request->status, 'updated_at' => now()]);
            } else {
                DB::table('intern_tasks')
                    ->where('task_id', $request->task_id)
                    ->where('assigned_by', $supervisorId)
                    ->update(['task_status' => $request->status, 'updated_at' => now()]);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
