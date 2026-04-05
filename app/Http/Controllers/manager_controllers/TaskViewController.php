<?php

namespace App\Http\Controllers\manager_controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TaskViewController extends Controller
{
    /**
     * Display tasks for interns under this manager (VIEW-ONLY)
     */
    public function index(Request $request)
    {
        $manager = Auth::guard('manager')->user();
        
        if (!$manager) {
            return redirect()->route('login');
        }
        
        // Get technologies this manager has permission for
        $allowedTechs = DB::table('manager_permissions')
            ->where('manager_id', $manager->manager_id)
            ->join('technologies', 'manager_permissions.tech_id', '=', 'technologies.tech_id')
            ->pluck('technologies.technology')
            ->toArray();
        
        // Base query for tasks - using intern_tasks table
        $query = DB::table('intern_tasks as t')
            ->join('intern_accounts as i', 't.eti_id', '=', 'i.eti_id')
            ->leftJoin('manager_accounts as s', 't.assigned_by', '=', 's.manager_id')
            ->select(
                't.task_id as id',
                't.task_title as title',
                't.task_description as description',
                't.task_status as status',
                't.task_end as deadline',
                't.task_points as points',
                't.grade',
                't.created_at',
                'i.name as intern_name',
                'i.eti_id',
                'i.int_technology as technology',
                's.name as supervisor_name'
            );
        
        // Filter by allowed technologies
        if (!empty($allowedTechs)) {
            $query->whereIn('i.int_technology', $allowedTechs);
        }
        
        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('t.task_title', 'LIKE', "%{$search}%")
                  ->orWhere('i.name', 'LIKE', "%{$search}%");
            });
        }
        
        // Apply status filter
        if ($request->filled('status')) {
            $query->where('t.task_status', $request->status);
        }
        
        // Apply date filters
        if ($request->filled('from_date')) {
            $query->whereDate('t.task_end', '>=', $request->from_date);
        }
        
        if ($request->filled('to_date')) {
            $query->whereDate('t.task_end', '<=', $request->to_date);
        }
        
        // Get tasks with pagination
        $tasks = $query->orderBy('t.task_end', 'asc')
                       ->paginate(15)
                       ->withQueryString();
        
        // Get interns list for filter dropdown
        $interns = DB::table('intern_accounts')
            ->when(!empty($allowedTechs), function($q) use ($allowedTechs) {
                $q->whereIn('int_technology', $allowedTechs);
            })
            ->select('int_id', 'name', 'eti_id')
            ->get();
        
        // Calculate statistics
        $baseQuery = DB::table('intern_tasks as t')
            ->join('intern_accounts as i', 't.eti_id', '=', 'i.eti_id');
        
        if (!empty($allowedTechs)) {
            $baseQuery->whereIn('i.int_technology', $allowedTechs);
        }
        
        $stats = [
            'total' => (clone $baseQuery)->count(),
            'pending' => (clone $baseQuery)->where('t.task_status', 'pending')->count(),
            'submitted' => (clone $baseQuery)->where('t.task_status', 'submitted')->count(),
            'approved' => (clone $baseQuery)->where('t.task_status', 'approved')->count(),
            'overdue' => (clone $baseQuery)
                ->where('t.task_end', '<', Carbon::now())
                ->whereNotIn('t.task_status', ['approved', 'completed'])
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
        
        if (!$manager) {
            return redirect()->route('login');
        }
        
        $task = DB::table('intern_tasks as t')
            ->join('intern_accounts as i', 't.eti_id', '=', 'i.eti_id')
            ->leftJoin('manager_accounts as s', 't.assigned_by', '=', 's.manager_id')
            ->where('t.task_id', $id)
            ->select(
                't.*',
                'i.name as intern_name',
                'i.eti_id',
                'i.email as intern_email',
                'i.int_technology as technology',
                's.name as supervisor_name'
            )
            ->first();
        
        if (!$task) {
            abort(404, 'Task not found');
        }
        
        return view('pages.manager.tasks.show', compact('task'));
    }
    
    /**
     * Export tasks to CSV
     */
    public function export(Request $request)
    {
        set_time_limit(0);
        ini_set('memory_limit', '512M');
        
        $manager = Auth::guard('manager')->user();
        
        if (!$manager) {
            return redirect()->route('login');
        }
        
        // Get technologies this manager has permission for
        $allowedTechs = DB::table('manager_permissions')
            ->where('manager_id', $manager->manager_id)
            ->join('technologies', 'manager_permissions.tech_id', '=', 'technologies.tech_id')
            ->pluck('technologies.technology')
            ->toArray();
        
        $query = DB::table('intern_tasks as t')
            ->join('intern_accounts as i', 't.eti_id', '=', 'i.eti_id')
            ->leftJoin('manager_accounts as s', 't.assigned_by', '=', 's.manager_id')
            ->select(
                't.task_id',
                't.task_title',
                't.task_status',
                't.task_end',
                't.task_points',
                't.grade',
                'i.name as intern_name',
                'i.eti_id',
                's.name as supervisor_name'
            );
        
        if (!empty($allowedTechs)) {
            $query->whereIn('i.int_technology', $allowedTechs);
        }
        
        if ($request->filled('status')) {
            $query->where('t.task_status', $request->status);
        }
        
        if ($request->filled('from_date')) {
            $query->whereDate('t.task_end', '>=', $request->from_date);
        }
        
        if ($request->filled('to_date')) {
            $query->whereDate('t.task_end', '<=', $request->to_date);
        }
        
        $fileName = 'tasks_export_' . date('Y-m-d') . '.csv';
        
        return response()->streamDownload(function() use ($query) {
            if (ob_get_level() > 0) ob_end_clean();
            
            $file = fopen('php://output', 'w');
            fputs($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
            
            fputcsv($file, [
                'Task ID', 'Task Title', 'Intern Name', 'Supervisor', 
                'Deadline', 'Points', 'Status', 'Grade'
            ]);
            
            foreach ($query->cursor() as $task) {
                fputcsv($file, [
                    $task->task_id,
                    $task->task_title,
                    $task->intern_name,
                    $task->supervisor_name ?? 'N/A',
                    $task->task_end,
                    $task->task_points ?? 'N/A',
                    ucfirst($task->task_status),
                    $task->grade ?? 'Pending'
                ]);
            }
            fclose($file);
        }, $fileName);
    }
}