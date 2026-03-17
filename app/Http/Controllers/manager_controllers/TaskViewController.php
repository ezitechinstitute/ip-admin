<?php

namespace App\Http\Controllers\manager_controllers;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\InternAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TaskViewController extends Controller
{
    /**
     * Display tasks for interns under this manager (VIEW-ONLY)
     */
    public function index(Request $request)
    {
        $manager = Auth::guard('manager')->user();
        
        // Get interns under this manager
        $internIds = InternAccount::where('manager_id', $manager->manager_id)
                                   ->pluck('int_id');
        
        $query = Task::whereIn('intern_id', $internIds)
                     ->with(['intern', 'supervisor']);

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('intern_id')) {
            $query->where('intern_id', $request->intern_id);
        }
        
        if ($request->filled('from_date')) {
            $query->whereDate('deadline', '>=', $request->from_date);
        }
        
        if ($request->filled('to_date')) {
            $query->whereDate('deadline', '<=', $request->to_date);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhereHas('intern', function($iq) use ($search) {
                      $iq->where('name', 'LIKE', "%{$search}%");
                  });
            });
        }
        
        $tasks = $query->orderBy('deadline', 'asc')
                       ->paginate(15)
                       ->withQueryString();
        
        // For filter dropdown
        $interns = InternAccount::where('manager_id', $manager->manager_id)->get();
        
        // Statistics
        $stats = [
            'total' => $query->count(),
            'pending' => Task::whereIn('intern_id', $internIds)->where('status', 'pending')->count(),
            'submitted' => Task::whereIn('intern_id', $internIds)->where('status', 'submitted')->count(),
            'overdue' => Task::whereIn('intern_id', $internIds)
                             ->where('deadline', '<', now())
                             ->whereIn('status', ['pending', 'submitted'])
                             ->count(),
            'completed' => Task::whereIn('intern_id', $internIds)
                               ->whereIn('status', ['approved', 'rejected'])
                               ->count(),
        ];
        
        return view('pages.manager.tasks.index', compact('tasks', 'interns', 'stats'));
    }

    /**
     * Show single task details (VIEW-ONLY)
     */
    public function show($id)
    {
        $manager = Auth::guard('manager')->user();
        
        $task = Task::with(['intern', 'supervisor'])
                    ->whereHas('intern', function($q) use ($manager) {
                        $q->where('manager_id', $manager->manager_id);
                    })
                    ->findOrFail($id);
        
        return view('pages.manager.tasks.show', compact('task'));
    }

    /**
     * Export tasks to CSV (VIEW-ONLY export)
     */
    public function export(Request $request)
    {
        $manager = Auth::guard('manager')->user();
        
        $internIds = InternAccount::where('manager_id', $manager->manager_id)
                                   ->pluck('int_id');
        
        $query = Task::whereIn('intern_id', $internIds)
                     ->with(['intern', 'supervisor']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('from_date')) {
            $query->whereDate('deadline', '>=', $request->from_date);
        }
        
        if ($request->filled('to_date')) {
            $query->whereDate('deadline', '<=', $request->to_date);
        }

        $fileName = 'tasks_export_' . date('Y-m-d') . '.csv';

        return response()->streamDownload(function() use ($query) {
            if (ob_get_level() > 0) ob_end_clean();
            
            $file = fopen('php://output', 'w');
            fputs($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($file, [
                'Task ID', 'Title', 'Intern', 'Supervisor', 'Deadline',
                'Points', 'Status', 'Grade', 'Created At'
            ]);

            foreach ($query->cursor() as $task) {
                fputcsv($file, [
                    $task->id,
                    $task->title,
                    $task->intern->name ?? 'N/A',
                    $task->supervisor->name ?? 'N/A',
                    $task->deadline,
                    $task->points,
                    ucfirst($task->status),
                    $task->grade ?? 'Pending',
                    $task->created_at->format('Y-m-d')
                ]);
            }
            fclose($file);
        }, $fileName);
    }
}