<?php

namespace App\Http\Controllers;

use App\Models\AdminSetting;
use App\Models\InternTask;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class InternTaskController extends Controller
{
    public function index(Request $request)
{
    $pageLimitSet = AdminSetting::first();
    $perPage = $request->input('per_page', $pageLimitSet->pagination_limit ?? 15);

    // English: Changed 'id' to 'task_id' to match your exact schema
    $query = InternTask::select('task_id', 'eti_id', 'task_title', 'task_status', 'created_at')
        ->with(['intern' => function($q) {
            $q->select('eti_id', 'name'); 
        }]);

    // 🔍 Search Optimization
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('task_title', 'like', "{$search}%")
              ->orWhere('eti_id', 'like', "{$search}%");
        });
    }

    // 🔘 Status Filter
    if ($request->filled('status')) {
        $query->where('task_status', $request->status);
    }

    // English: Sorting by 'task_id' instead of 'id' to fix the 500 error
    $query->orderBy('task_id', 'desc'); 

    // 🔢 Pagination
    $tasks = $query->paginate($perPage)->withQueryString();

    return view('pages.admin.intern-task.internTask', compact('tasks', 'perPage'));
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
    // English: Prevent timeouts and set a safe memory limit for 200k+ rows
    set_time_limit(0);
    ini_set('memory_limit', '512M');

    $fileName = 'intern_tasks_export_' . date('Y-m-d_H-i-s') . '.csv';

    // 1. Optimized Query (English: We exclude 'task_screenshot' to save bandwidth/RAM)
    $query = InternTask::select([
        'task_id', 'eti_id', 'task_title', 'task_start', 'task_end', 
        'task_duration', 'task_days', 'task_status', 'task_obt_points', 
        'task_points', 'review', 'submit_description', 'task_live_url', 'task_git_url'
    ])->with(['intern' => function($q) {
        $q->select('eti_id', 'name'); // English: Selective eager loading
    }]);

    // 🔍 Search Optimization
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            // English: Prefix search for index efficiency
            $q->where('task_title', 'like', "{$search}%")
              ->orWhereHas('intern', function ($sub) use ($search) {
                  $sub->where('name', 'like', "{$search}%");
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

    $columns = ['Intern Name', 'ETI ID', 'Task Title', 'Start Date', 'End Date', 'Duration', 'Days', 'Status', 'Obtained Points', 'Total Points', 'Review', 'Description', 'Task Live URL', 'Task Git URL'];

    // 2. Stream Response (English: Using cursor() to handle 200,000 rows without RAM spikes)
    return response()->stream(function() use ($query, $columns) {
        $file = fopen('php://output', 'w');
        
        // Add UTF-8 BOM for Excel
        fputs($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
        fputcsv($file, $columns);

        
        // English: cursor() processes one row at a time, keeping memory flat
        foreach ($query->orderBy('task_id', 'desc')->cursor() as $task) {
            fputcsv($file, [
                $task->intern->name ?? 'N/A',
                $task->eti_id,
                $task->task_title,
                $task->task_start,
                $task->task_end,
                $task->task_duration,
                $task->task_days,
                ucfirst($task->task_status),
                $task->task_obt_points ?? '',
                $task->task_points ?? '',
                $task->review,
                $task->submit_description,
                $task->task_live_url,
                $task->task_git_url,
            ]);

            // English: Periodically flush data to the browser
            flush();
        }
        fclose($file);
    }, 200, $headers);
}
}
