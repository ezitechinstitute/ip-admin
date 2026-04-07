<?php

namespace App\Http\Controllers\intern;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class InternTaskController extends Controller
{
    public function index()
    {
        $intern = Auth::guard('intern')->user();
        
        if (!$intern) {
            return redirect()->route('login');
        }
        
        // ✅ FIXED: Use 'tasks' table instead of 'intern_tasks'
        $tasks = DB::table('tasks')
            ->where('intern_id', $intern->int_id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        // ✅ FIXED: Get task statistics from 'tasks' table
        $stats = [
            'total' => DB::table('tasks')->where('intern_id', $intern->int_id)->count(),
            'pending' => DB::table('tasks')->where('intern_id', $intern->int_id)->where('status', 'pending')->count(),
            'submitted' => DB::table('tasks')->where('intern_id', $intern->int_id)->where('status', 'submitted')->count(),
            'approved' => DB::table('tasks')->where('intern_id', $intern->int_id)->where('status', 'approved')->count(),
            'rejected' => DB::table('tasks')->where('intern_id', $intern->int_id)->where('status', 'rejected')->count(),
        ];
        
        return view('pages.intern.tasks.index', compact('tasks', 'stats'));
    }
    
    public function show($id)
    {
        $intern = Auth::guard('intern')->user();
        
        if (!$intern) {
            return redirect()->route('login');
        }
        
        // ✅ FIXED: Use 'tasks' table
        $task = DB::table('tasks')
            ->where('id', $id)
            ->where('intern_id', $intern->int_id)
            ->first();
        
        if (!$task) {
            abort(404, 'Task not found');
        }
        
        // Check if task can be resubmitted
        $canResubmit = in_array($task->status, ['rejected', 'pending']);
        $isSubmitted = $task->status == 'submitted';
        $isApproved = in_array($task->status, ['approved']);
        
        return view('pages.intern.tasks.show', compact('task', 'canResubmit', 'isSubmitted', 'isApproved'));
    }
    
    public function submit(Request $request, $id)
    {
        $intern = Auth::guard('intern')->user();
        
        if (!$intern) {
            return redirect()->route('login');
        }
        
        // ✅ FIXED: Use 'tasks' table
        $task = DB::table('tasks')
            ->where('id', $id)
            ->where('intern_id', $intern->int_id)
            ->first();
        
        if (!$task) {
            abort(404, 'Task not found');
        }
        
        // Check if task can be submitted
        if (in_array($task->status, ['approved'])) {
            return redirect()->back()->with('error', 'This task is already completed. You cannot resubmit.');
        }
        
        $validated = $request->validate([
            'github_url' => 'nullable|url|max:500',
            'live_url' => 'nullable|url|max:500',
            'screenshot' => 'nullable|file|image|mimes:jpeg,png,jpg,gif|max:2048',
            'submission_notes' => 'nullable|string|max:1000',
        ]);
        
        $updateData = [
            'github_url' => $validated['github_url'] ?? null,
            'live_url' => $validated['live_url'] ?? null,
            'submission_notes' => $validated['submission_notes'] ?? null,
            'status' => 'submitted',
            'submitted_at' => now(),
            'updated_at' => now(),
        ];
        
        // Handle file upload
        if ($request->hasFile('screenshot')) {
            $file = $request->file('screenshot');
            $fileName = time() . '_' . $intern->int_id . '_' . $id . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('uploads/task_submissions', $fileName, 'public');
            $updateData['submission_notes'] = ($updateData['submission_notes'] ?? '') . "\n[Screenshot: storage/" . $path . "]";
        }
        
        DB::table('tasks')
            ->where('id', $id)
            ->update($updateData);
        
        // Create notification for intern
        $this->createSubmissionNotification($task, $intern);
        
        return redirect()->route('intern.tasks.show', $id)
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
            'message' => 'You have successfully submitted "' . $task->title . '". Please wait for supervisor review.',
            'type' => 'task',
            'is_read' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}