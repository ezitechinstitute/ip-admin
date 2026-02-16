<?php

namespace App\Http\Controllers;
use App\Models\AdminSetting;
use App\Models\ProjectTask;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ProjectTaskController extends Controller
{
    public function index(Request $request){
        $pageLimitSet = AdminSetting::first();
        $perPage = $request->input('per_page', $pageLimitSet->pagination_limit ?? 15);

    $tasks = ProjectTask::with(['intern', 'project'])
        ->when($request->filled('search'), function ($query) use ($request) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('task_title', 'like', "%{$search}%")
                  ->orWhereHas('intern', function ($sub) use ($search) {
                      $sub->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('project', function ($sub) use ($search) {
                      $sub->where('title', 'like', "%{$search}%");
                  });
            });
        })
        ->when($request->filled('status'), function ($query) use ($request) {
            $query->where('task_status', $request->status);
        })
        ->orderByDesc('created_at')
        ->paginate($perPage)
        ->withQueryString();
        // return $tasks;
        return view(
        'pages.admin.project-task.projectTask',
        compact('tasks', 'perPage')
    );
    }
    public function updateProjecTask(Request $request){
        $request->validate([
            'task_title' => 'required',
            'task_status' => 'required'
        ]);
        ProjectTask::where('task_id', $request->task_id)->update([
            'task_title' => $request->task_title,
            'task_status' => $request->task_status
        ]);
        return redirect()->back()->with('success', 'Project Task Successfully updated!');

    }


    public function exportProjectTasksCSV(Request $request)
{
    $fileName = 'project_tasks_' . date('Y-m-d_H-i-s') . '.csv';

    // Same logic as index for filtering
    $tasks = ProjectTask::with(['intern', 'project'])
        ->when($request->filled('search'), function ($query) use ($request) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('task_title', 'like', "%{$search}%")
                  ->orWhereHas('intern', function ($sub) use ($search) {
                      $sub->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('project', function ($sub) use ($search) {
                      $sub->where('title', 'like', "%{$search}%");
                  });
            });
        })
        ->when($request->filled('status'), function ($query) use ($request) {
            $query->where('task_status', $request->status);
        })
        ->get();

    $headers = array(
        "Content-type"        => "text/csv",
        "Content-Disposition" => "attachment; filename=$fileName",
        "Pragma"              => "no-cache",
        "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
        "Expires"             => "0"
    );

    // Column titles
    $columns = array('Intern Name', 'Task Title', 'Project Title', 'Start Date', 'End Date', 'Duration', 'Days', 'Status', 'Review', 'Task Live URL', 'Task Git URL', 'Description');

    $callback = function() use($tasks, $columns) {
        $file = fopen('php://output', 'w');
        fputcsv($file, $columns);

        foreach ($tasks as $task) {
            fputcsv($file, array(
                $task->intern->name ?? 'N/A',
                $task->task_title,
                $task->project->title ?? 'N/A',
                $task->t_start_date,
                $task->t_end_date,
                $task->task_duration,
                $task->task_days,
                ucfirst($task->task_status),
                $task->review,
                $task->task_live_url,
                $task->task_git_url,
                $task->description,
            ));
        }
        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}
}
