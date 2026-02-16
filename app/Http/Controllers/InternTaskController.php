<?php

namespace App\Http\Controllers;

use App\Models\AdminSetting;
use App\Models\InternTask;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class InternTaskController extends Controller
{
    public function index(Request $request){
        $pageLimitSet = AdminSetting::first();
        $perPage = $request->input('per_page', $pageLimitSet->pagination_limit ?? 15);

    $tasks = InternTask::with('intern')
        ->when($request->filled('search'), function ($query) use ($request) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('task_title', 'like', "%{$search}%")
                  ->orWhereHas('intern', function ($sub) use ($search) {
                      $sub->where('name', 'like', "%{$search}%");
                  });
            });
        })
        ->when($request->filled('status'), function ($query) use ($request) {
            $query->where('task_status', $request->status);
        })
        ->orderByDesc('created_at')
        ->paginate($perPage)
        ->withQueryString();

        return view('pages.admin.intern-task.internTask',
        compact('tasks', 'perPage'));
    }


    public function updateInternTask(Request $request){
        $request->validate([
            'task_title' => 'required',
            'task_status' => 'required'
        ]);
        InternTask::where('task_id', $request->task_id)->update([
            'task_title' => $request->task_title,
            'task_status' => $request->task_status
        ]);
        return redirect()->back()->with('success', 'Project Task Successfully updated!');

    }


    public function exportInternTasksCSV(Request $request)
{
    // File name with current timestamp
    $fileName = 'intern_tasks_export_' . date('Y-m-d_H-i-s') . '.csv';

    // Fetching data based on your index logic
    $tasks = InternTask::with(['intern'])
        ->when($request->filled('search'), function ($query) use ($request) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('task_title', 'like', "%{$search}%")
                  ->orWhereHas('intern', function ($sub) use ($search) {
                      $sub->where('name', 'like', "%{$search}%");
                  });
            });
        })
        ->when($request->filled('status'), function ($query) use ($request) {
            $query->where('task_status', $request->status);
        })
        ->get();

    $headers = [
        "Content-type"        => "text/csv",
        "Content-Disposition" => "attachment; filename=$fileName",
        "Pragma"              => "no-cache",
        "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
        "Expires"             => "0"
    ];

    // Column titles for the CSV file
    $columns = [
        'Intern Name', 
        'ETI ID', 
        'Task Title', 
        'Start Date', 
        'End Date', 
        'Duration', 
        'Days', 
        'Status', 
        'Obtained Points', 
        'Total Points', 
        'Review', 
        'Description', 
        'Task Live URL',
        'Task Git URL'
    ];

    $callback = function() use($tasks, $columns) {
        $file = fopen('php://output', 'w');
        
        // Add UTF-8 BOM for proper Excel rendering (fixes special characters)
        fputs($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
        
        // Write the header columns
        fputcsv($file, $columns);

        foreach ($tasks as $task) {
            fputcsv($file, [
                $task->intern->name ?? 'N/A',      // From InternAccount model
                $task->eti_id,
                $task->task_title,
                $task->task_start,
                $task->task_end,
                $task->task_duration,
                $task->task_days,
                ucfirst($task->task_status),
                $task->task_obt_points ?? '',      // Show empty cell if null
                $task->task_points ?? '',          // Show empty cell if null
                $task->review,
                $task->submit_description,
                $task->task_live_url,
                $task->task_git_url,
            ]);
        }
        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}
}
