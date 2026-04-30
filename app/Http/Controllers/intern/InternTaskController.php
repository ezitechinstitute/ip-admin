<?php

namespace App\Http\Controllers\intern;
use App\Helpers\PortalFreezeHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;

class InternTaskController extends Controller
{
    public function index()
    {
        $intern = Auth::guard('intern')->user();
        
        if (!$intern) {
            return redirect()->route('login');
        }
        
        // Get all tasks for this intern from BOTH intern_tasks and project_tasks
        $internTasks = DB::table('intern_tasks')
            ->where('eti_id', $intern->eti_id)
            ->select(
                'task_id',
                'eti_id',
                'task_title',
                'task_description',
                'task_start',
                'task_end',
                'task_duration',
                'task_days',
                'task_points',
                'task_obt_points',
                'assigned_by',
                'task_status',
                'task_screenshot',
                'task_live_url',
                'task_git_url',
                'submit_description',
                'created_at',
                'updated_at',
                DB::raw("'intern' as task_source")
            );

        $projectTasks = DB::table('project_tasks')
            ->where('eti_id', $intern->eti_id)
            ->select(
                'task_id',
                'eti_id',
                'task_title',
                'description as task_description',
                't_start_date as task_start',
                't_end_date as task_end',
                'task_duration',
                'task_days',
                'task_mark as task_points',
                'task_obt_mark as task_obt_points',
                'assigned_by',
                'task_status',
                'task_screenshot',
                'task_live_url',
                'task_git_url',
                DB::raw("'' as submit_description"),
                'created_at',
                'updated_at',
                DB::raw("'project' as task_source")
            );

        // Union both queries
        $allTasksCollection = $internTasks->union($projectTasks)
            ->orderBy('created_at', 'desc')
            ->get();

        // For pagination (table view) - manually paginate collection
        $perPage = 10;
        $page = Paginator::resolveCurrentPage();
        $tasks = new LengthAwarePaginator(
            collect($allTasksCollection)->forPage($page, $perPage)->values(),
            count($allTasksCollection),
            $perPage,
            $page,
            [
                'path' => route('intern.portal.tasks'),
                'query' => request()->query(),
            ]
        );

        // For Kanban & Timeline Views (ALL tasks - no pagination)
        $allTasks = $allTasksCollection;
        
        // Get task statistics (counting from both tables)
        $internStats = [
            'total' => DB::table('intern_tasks')->where('eti_id', $intern->eti_id)->count(),
            'pending' => DB::table('intern_tasks')->where('eti_id', $intern->eti_id)->whereIn('task_status', ['pending', 'Assigned'])->count(),
            'submitted' => DB::table('intern_tasks')->where('eti_id', $intern->eti_id)->where('task_status', 'submitted')->count(),
            'approved' => DB::table('intern_tasks')->where('eti_id', $intern->eti_id)->whereIn('task_status', ['Completed', 'approved'])->count(),
            'rejected' => DB::table('intern_tasks')->where('eti_id', $intern->eti_id)->where('task_status', 'Rejected')->count(),
        ];

        $projectStats = [
            'total' => DB::table('project_tasks')->where('eti_id', $intern->eti_id)->count(),
            'pending' => DB::table('project_tasks')->where('eti_id', $intern->eti_id)->whereIn('task_status', ['pending', 'Assigned'])->count(),
            'submitted' => DB::table('project_tasks')->where('eti_id', $intern->eti_id)->where('task_status', 'submitted')->count(),
            'approved' => DB::table('project_tasks')->where('eti_id', $intern->eti_id)->whereIn('task_status', ['Completed', 'approved'])->count(),
            'rejected' => DB::table('project_tasks')->where('eti_id', $intern->eti_id)->where('task_status', 'Rejected')->count(),
        ];

        // Merge statistics from both tables
        $stats = [
            'total' => $internStats['total'] + $projectStats['total'],
            'pending' => $internStats['pending'] + $projectStats['pending'],
            'submitted' => $internStats['submitted'] + $projectStats['submitted'],
            'approved' => $internStats['approved'] + $projectStats['approved'],
            'rejected' => $internStats['rejected'] + $projectStats['rejected'],
        ];
        
        return view('pages.intern.tasks.index', compact('tasks','allTasks', 'stats'));
    }
    
    public function show($id)
    {
        $intern = Auth::guard('intern')->user();
        
        if (!$intern) {
            return redirect()->route('login');
        }
        
        // Try to get task from intern_tasks first
        $task = DB::table('intern_tasks')
            ->where('task_id', $id)
            ->where('eti_id', $intern->eti_id)
            ->first();

        // If not found, try project_tasks
        if (!$task) {
            $task = DB::table('project_tasks')
                ->where('task_id', $id)
                ->where('eti_id', $intern->eti_id)
                ->first();

            // Map project task fields to intern task format for consistency
            if ($task) {
                $task->task_description = $task->description;
                $task->task_start = $task->t_start_date;
                $task->task_end = $task->t_end_date;
                $task->task_points = $task->task_mark;
                $task->task_obt_points = $task->task_obt_mark;
                $task->task_source = 'project';
            }
        } else {
            $task->task_source = 'intern';
        }
        
        if (!$task) {
            abort(404, 'Task not found');
        }
        
        // Check if task can be resubmitted
        $canResubmit = in_array($task->task_status, ['Rejected', 'Assigned', 'pending']);
        $isSubmitted = $task->task_status == 'submitted';
        $isApproved = in_array($task->task_status, ['Completed', 'approved']);
        
        return view('pages.intern.tasks.show', compact('task', 'canResubmit', 'isSubmitted', 'isApproved'));
    }
    
    public function submit(Request $request, $id)
    {
        $intern = Auth::guard('intern')->user();
        
        if (!$intern) {
            return redirect()->route('login');
        }

        // ✅ Portal Freeze Check 
        $freezeStatus = PortalFreezeHelper::getStatus($intern->email);
        if ($freezeStatus['frozen']) {
            return back()->with('error', $freezeStatus['message']);
        }
        
        // Try to get task from intern_tasks first
        $task = DB::table('intern_tasks')
            ->where('task_id', $id)
            ->where('eti_id', $intern->eti_id)
            ->first();
        
        $taskSource = 'intern'; // Default to intern
        
        // If not found, try project_tasks
        if (!$task) {
            $task = DB::table('project_tasks')
                ->where('task_id', $id)
                ->where('eti_id', $intern->eti_id)
                ->first();
            $taskSource = 'project';
        }
        
        if (!$task) {
            abort(404, 'Task not found');
        }
        
        // Check if task can be submitted
        if (in_array($task->task_status, ['Completed', 'approved'])) {
            return redirect()->back()->with('error', 'This task is already completed. You cannot resubmit.');
        }
        
        $validated = $request->validate([
            'task_git_url' => 'nullable|url|max:500',
            'task_live_url' => 'nullable|url|max:500',
            'task_screenshot' => 'nullable|file|image|mimes:jpeg,png,jpg,gif|max:2048',
            'submit_description' => 'nullable|string|max:1000',
        ]);
        
        $updateData = [
            'task_status' => 'submitted',
            'updated_at' => now(),
        ];
        
        // Only add fields if they are provided
        if (!empty($validated['task_git_url'])) {
            $updateData['task_git_url'] = $validated['task_git_url'];
        }
        
        if (!empty($validated['task_live_url'])) {
            $updateData['task_live_url'] = $validated['task_live_url'];
        }
        
        // Add submit_description only for intern tasks if provided
        if ($taskSource === 'intern' && !empty($validated['submit_description'])) {
            $updateData['submit_description'] = $validated['submit_description'];
        }
        
        // Handle file upload
        if ($request->hasFile('task_screenshot')) {
            $file = $request->file('task_screenshot');
            $fileName = time() . '_' . $intern->int_id . '_' . $id . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('uploads/task_submissions', $fileName, 'public');
            $updateData['task_screenshot'] = 'storage/' . $path;
            
            // Delete old screenshot if exists
            if ($task->task_screenshot && $task->task_screenshot != 'NULL' && Storage::disk('public')->exists(str_replace('storage/', '', $task->task_screenshot))) {
                Storage::disk('public')->delete(str_replace('storage/', '', $task->task_screenshot));
            }
        }
        
        // Update the correct table
        if ($taskSource === 'intern') {
            DB::table('intern_tasks')
                ->where('task_id', $id)
                ->update($updateData);
        } else {
            DB::table('project_tasks')
                ->where('task_id', $id)
                ->update($updateData);
        }
        
        // Create notification for intern
        $this->createSubmissionNotification($task, $intern);
        
        return redirect()->route('intern.portal.tasks')
            ->with('success', 'Task submitted successfully! Waiting for supervisor review.');
    }
    
    private function createSubmissionNotification($task, $intern)
    {
        if (!Schema::hasTable('intern_notifications')) {
            return;
        }
        
        DB::table('intern_notifications')->insert([
            'intern_id' => $intern->int_id,
            'title' => 'Task Submitted',
            'message' => 'You have successfully submitted "' . $task->task_title . '". Please wait for supervisor review.',
            'type' => 'task',
            'is_read' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
  
}