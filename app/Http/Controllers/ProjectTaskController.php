<?php

namespace App\Http\Controllers;
use App\Models\AdminSetting;
use App\Models\ProjectTask;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ProjectTaskController extends Controller
{
    public function index(Request $request)
{
    $pageLimitSet = AdminSetting::first();
    $perPage = $request->input('per_page', $pageLimitSet->pagination_limit ?? 15);

    // English: Changed 'id' to 'task_id' to match your table schema
    $query = ProjectTask::select('task_id', 'project_id', 'eti_id', 'task_title', 'task_status', 'created_at')
        ->with([
            'intern' => function($q) {
                $q->select('eti_id', 'name'); // English: Use your primary key 'eti_id'
            },
            'project' => function($q) {
                $q->select('project_id', 'title'); // English: Use your primary key 'project_id'
            }
        ]);

    // 🔍 Search Optimization
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('task_title', 'like', "{$search}%")
              ->orWhere('eti_id', 'like', "{$search}%");
        });
    }

    // 🔘 Status filter
    if ($request->filled('status')) {
        $query->where('task_status', $request->status);
    }

    // English: Sorting by 'task_id' instead of 'id' to fix the 500 error
    $query->orderBy('task_id', 'desc');

    // 🔢 Pagination
    $tasks = $query->paginate($perPage)->withQueryString();

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
    // English: Prevent timeouts and increase memory for massive task data
    set_time_limit(0);
    ini_set('memory_limit', '512M');

    $fileName = 'project_tasks_' . date('Y-m-d_H-i-s') . '.csv';

    // 1. Optimized Query (English: We exclude heavy columns like task_screenshot)
    $query = ProjectTask::select([
        'task_id', 'project_id', 'eti_id', 'task_title', 't_start_date', 
        't_end_date', 'task_duration', 'task_days', 'task_status', 
        'review', 'task_live_url', 'task_git_url', 'description'
    ])->with([
        'intern' => function($q) { $q->select('eti_id', 'name'); },
        'project' => function($q) { $q->select('project_id', 'title'); }
    ]);

    // 🔍 Search Optimization
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            // English: Use prefix search for better index performance
            $q->where('task_title', 'like', "{$search}%")
              ->orWhereHas('intern', function ($sub) use ($search) {
                  $sub->where('name', 'like', "{$search}%");
              })
              ->orWhereHas('project', function ($sub) use ($search) {
                  $sub->where('title', 'like', "{$search}%");
              });
        });
    }

    if ($request->filled('status')) {
        $query->where('task_status', $request->status);
    }

    $headers = [
        "Content-type"        => "text/csv",
        "Content-Disposition" => "attachment; filename=$fileName",
        "Pragma"              => "no-cache",
        "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
        "Expires"             => "0"
    ];

    $columns = ['Intern Name', 'Task Title', 'Project Title', 'Start Date', 'End Date', 'Duration', 'Days', 'Status', 'Review', 'Task Live URL', 'Task Git URL', 'Description'];

    // 2. Stream Response (English: Using cursor() to handle 200k+ rows efficiently)
    return response()->stream(function() use ($query, $columns) {
        $file = fopen('php://output', 'w');
        fputcsv($file, $columns);

        // 
        // English: cursor() keeps only one row in memory at a time
        foreach ($query->orderBy('task_id', 'desc')->cursor() as $task) {
            fputcsv($file, [
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
            ]);

            // English: Send data to browser in chunks to avoid server hang
            flush();
        }
        fclose($file);
    }, 200, $headers);
}
}
