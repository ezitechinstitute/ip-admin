<?php

namespace App\Http\Controllers\supervisor_controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SupervisorProjectController extends Controller
{
    public function index()
    {
        $supervisorId = \Illuminate\Support\Facades\Auth::guard('manager')->id() ?? session('manager_id');
        $supervisorTechnology = trim(session('manager_department'));

        if (!$supervisorId) {
            return redirect()->route('login')->with('error', 'Authentication error: Supervisor ID not found. Please re-login.');
        }

        $projects = \Illuminate\Support\Facades\DB::table('intern_projects')
            ->join('manager_accounts', 'intern_projects.assigned_by', '=', 'manager_accounts.manager_id')
            ->select(
                'intern_projects.project_id',
                'intern_projects.eti_id',
                'intern_projects.email',
                'intern_projects.title',
                'intern_projects.start_date',
                'intern_projects.end_date',
                'intern_projects.assigned_by',
                'intern_projects.pstatus',
                'intern_projects.tech_stack',
                'intern_projects.difficulty_level',
                'manager_accounts.name as supervisor_name'
            )
            ->where('intern_projects.assigned_by', $supervisorId)
            ->orderByDesc('intern_projects.project_id')
            ->limit(20)
            ->get();

        // Fetch active interns for this supervisor to show in the "Create Project" dropdown
        $interns = \Illuminate\Support\Facades\DB::table('intern_accounts')
            ->select('eti_id', 'name', 'email')
            ->whereRaw('LOWER(int_status) = ?', ['active'])
            ->when($supervisorTechnology, function ($query, $supervisorTechnology) {
                $query->whereRaw('LOWER(int_technology) = ?', [strtolower($supervisorTechnology)]);
            })
            ->get();

        return view('content.supervisor.projects', compact('projects', 'interns'));
    }


public function store(Request $request)
{
    $request->validate([
        'eti_id' => 'required',
        'email' => 'required|email',
        'title' => 'required|string|max:255',
        'start_date' => 'required',
        'end_date' => 'required',
        'duration' => 'required|numeric',
        'days' => 'required|numeric',
        'project_marks' => 'required',
        'obt_marks' => 'nullable|numeric',
        'description' => 'required|string',
        'tech_stack' => 'nullable|string|max:255',
        'difficulty_level' => 'required|string|in:Beginner,Intermediate,Advanced',
        'pstatus' => 'required|string|in:Ongoing,Submitted,Approved,Rejected,Expired,Completed,Pending',
    ]);

    \Illuminate\Support\Facades\DB::table('intern_projects')->insert([
        'eti_id' => $request->eti_id,
        'email' => $request->email,
        'title' => $request->title,
        'start_date' => $request->start_date,
        'end_date' => $request->end_date,
        'duration' => $request->duration,
        'days' => $request->days,
        'project_marks' => $request->project_marks,
        'obt_marks' => $request->obt_marks ?? 0,
        'description' => $request->description,
        'tech_stack' => $request->tech_stack,
        'difficulty_level' => $request->difficulty_level,
        'assigned_by' => \Illuminate\Support\Facades\Auth::guard('manager')->id() ?? session('manager_id'),
        'pstatus' => $request->pstatus,
        'createdat' => now(),
        'updatedat' => now(),
    ]);

        $this->logActivity('Created Project', 'Project title: ' . $request->title . ' for Intern: ' . $request->eti_id);
        $this->notifyIntern($request->eti_id, 'New Project', 'You have been assigned a new project: ' . $request->title);

        return redirect()->back()->with('success', 'Project created successfully');
}

public function tasks($project_id)
{
    $project = \Illuminate\Support\Facades\DB::table('intern_projects')
        ->where('project_id', $project_id)
        ->first();

    $tasks = \Illuminate\Support\Facades\DB::table('project_tasks')
        ->where('project_id', $project_id)
        ->orderByDesc('task_id')
        ->get();

    return view('content.supervisor.project-tasks', compact('project', 'tasks'));
}


    public function storeTask(Request $request, $project_id)
    {
        $request->validate([
            'task_title' => 'required|string|max:255',
            'milestone_title' => 'nullable|string|max:255',
            't_start_date' => 'required',
            't_end_date' => 'required',
            'task_days' => 'required|numeric',
            'task_duration' => 'required|numeric',
            'task_obt_mark' => 'nullable|numeric',
            'task_mark' => 'required|numeric',
            'task_status' => 'required|string',
            'description' => 'required|string',
        ]);

        $project = \Illuminate\Support\Facades\DB::table('intern_projects')
            ->where('project_id', $project_id)
            ->first();

        if (!$project) {
            return back()->with('error', 'Project not found.');
        }

        \Illuminate\Support\Facades\DB::table('project_tasks')->insert([
            'project_id' => $project_id,
            'eti_id' => $project->eti_id,
            'task_title' => $request->task_title,
            'milestone_title' => $request->milestone_title,
            't_start_date' => $request->t_start_date,
            't_end_date' => $request->t_end_date,
            'task_days' => $request->task_days,
            'task_duration' => $request->task_duration,
            'task_obt_mark' => $request->task_obt_mark ?? 0,
            'task_mark' => $request->task_mark,
            'assigned_by' => \Illuminate\Support\Facades\Auth::guard('manager')->id() ?? session('manager_id'),
            'task_status' => $request->task_status,
            'approved' => null,
            'review' => '',
            'task_screenshot' => '',
            'task_live_url' => '',
            'task_git_url' => '',
            'description' => $request->description,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('supervisor.projects.tasks', $project_id)
            ->with('success', 'Task created successfully.');
    }

    public function editTask($project_id, $task_id)
    {
        $project = \Illuminate\Support\Facades\DB::table('intern_projects')->where('project_id', $project_id)->first();
        $task = \Illuminate\Support\Facades\DB::table('project_tasks')->where('task_id', $task_id)->first();

        if (!$project || !$task) {
            return back()->with('error', 'Task or Project not found.');
        }

        return view('content.supervisor.projects.edit-task', compact('project', 'task'));
    }

    public function updateTask(Request $request, $project_id, $task_id)
    {
        $request->validate([
            'task_title' => 'required|string|max:255',
            'milestone_title' => 'nullable|string|max:255',
            't_start_date' => 'required',
            't_end_date' => 'required',
            'task_days' => 'required|numeric',
            'task_duration' => 'required|numeric',
            'task_obt_mark' => 'nullable|numeric',
            'task_mark' => 'required|numeric',
            'task_status' => 'required|string',
            'description' => 'required|string',
        ]);

        \Illuminate\Support\Facades\DB::table('project_tasks')
            ->where('task_id', $task_id)
            ->where('project_id', $project_id)
            ->update([
                'task_title' => $request->task_title,
                'milestone_title' => $request->milestone_title,
                't_start_date' => $request->t_start_date,
                't_end_date' => $request->t_end_date,
                'task_days' => $request->task_days,
                'task_duration' => $request->task_duration,
                'task_obt_mark' => $request->task_obt_mark ?? 0,
                'task_mark' => $request->task_mark,
                'task_status' => $request->task_status,
                'description' => $request->description,
                'updated_at' => now(),
            ]);

        return redirect()->route('supervisor.projects.tasks', $project_id)
            ->with('success', 'Task updated successfully.');
    }

    public function deleteTask($project_id, $task_id)
    {
        \Illuminate\Support\Facades\DB::table('project_tasks')
            ->where('project_id', $project_id)
            ->where('task_id', $task_id)
            ->delete();

        return redirect()->route('supervisor.projects.tasks', $project_id)
            ->with('success', 'Task deleted successfully.');
    }
    public function edit($id)
    {
        $supervisorId = \Illuminate\Support\Facades\Auth::guard('manager')->id() ?? session('manager_id');
        $project = \Illuminate\Support\Facades\DB::table('intern_projects')
            ->where('project_id', $id)
            ->where('assigned_by', $supervisorId)
            ->first();

        if (!$project) {
            return redirect()->route('supervisor.projects')->with('error', 'Project not found.');
        }

        return view('content.supervisor.projects.edit', compact('project'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'duration' => 'required|numeric',
            'days' => 'required|numeric',
            'project_marks' => 'required',
            'obt_marks' => 'nullable|numeric',
            'description' => 'required|string',
            'tech_stack' => 'nullable|string|max:255',
            'difficulty_level' => 'required|string|in:Beginner,Intermediate,Advanced',
            'pstatus' => 'required|string|in:Ongoing,Submitted,Approved,Rejected,Expired,Completed,Pending',
        ]);

        $supervisorId = \Illuminate\Support\Facades\Auth::guard('manager')->id() ?? session('manager_id');

        \Illuminate\Support\Facades\DB::table('intern_projects')
            ->where('project_id', $id)
            ->where('assigned_by', $supervisorId)
            ->update([
                'title' => $request->title,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'duration' => $request->duration,
                'days' => $request->days,
                'project_marks' => $request->project_marks,
                'obt_marks' => $request->obt_marks ?? 0,
                'description' => $request->description,
                'tech_stack' => $request->tech_stack,
                'difficulty_level' => $request->difficulty_level,
                'pstatus' => $request->pstatus,
                'updatedat' => now(),
            ]);

        $this->logActivity('Updated Project', "Modified Project ID: {$id}");

        return redirect()->route('supervisor.projects')->with('success', 'Project updated successfully.');
    }

    public function destroy($id)
    {
        $supervisorId = \Illuminate\Support\Facades\Auth::guard('manager')->id() ?? session('manager_id');
        $project = \Illuminate\Support\Facades\DB::table('intern_projects')
            ->where('project_id', $id)
            ->where('assigned_by', $supervisorId)
            ->first();

        if ($project) {
            // Delete associated tasks first
            \Illuminate\Support\Facades\DB::table('project_tasks')->where('project_id', $id)->delete();
            // Delete project
            \Illuminate\Support\Facades\DB::table('intern_projects')->where('project_id', $id)->delete();
            
            $this->logActivity('Deleted Project', "Removed Project: '{$project->title}'");
            
            return redirect()->route('supervisor.projects')->with('success', 'Project and its tasks deleted successfully.');
        }

        return redirect()->route('supervisor.projects')->with('error', 'Project not found.');
    }
}