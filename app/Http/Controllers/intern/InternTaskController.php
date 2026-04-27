<?php

namespace App\Http\Controllers\intern;
use App\Helpers\PortalFreezeHelper;
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
        
        // Get all tasks for this intern
        $tasks = DB::table('intern_tasks')
            ->where('eti_id', $intern->eti_id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

         // For Kanban & Timeline Views (ALL tasks - future proof)
         $allTasks = DB::table('intern_tasks')
        ->where('eti_id', $intern->eti_id)
        ->orderBy('created_at', 'desc')
        ->get();
        
        // Get task statistics
        $stats = [
            'total' => DB::table('intern_tasks')->where('eti_id', $intern->eti_id)->count(),
            // FIXED: Include both 'pending' AND 'Assigned' statuses
            'pending' => DB::table('intern_tasks')->where('eti_id', $intern->eti_id)->whereIn('task_status', ['pending', 'Assigned'])->count(),
            'submitted' => DB::table('intern_tasks')->where('eti_id', $intern->eti_id)->where('task_status', 'submitted')->count(),
            'approved' => DB::table('intern_tasks')->where('eti_id', $intern->eti_id)->whereIn('task_status', ['Completed', 'approved'])->count(),
            'rejected' => DB::table('intern_tasks')->where('eti_id', $intern->eti_id)->where('task_status', 'Rejected')->count(),
        ];
        
        return view('pages.intern.tasks.index', compact('tasks','allTasks', 'stats'));
    }
    
    public function show($id)
    {
        $intern = Auth::guard('intern')->user();
        
        if (!$intern) {
            return redirect()->route('login');
        }
        
        $task = DB::table('intern_tasks')
            ->where('task_id', $id)
            ->where('eti_id', $intern->eti_id)
            ->first();
        
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
        
        $task = DB::table('intern_tasks')
            ->where('task_id', $id)
            ->where('eti_id', $intern->eti_id)
            ->first();
        
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
            'task_git_url' => $validated['task_git_url'] ?? null,
            'task_live_url' => $validated['task_live_url'] ?? null,
            'submit_description' => $validated['submit_description'] ?? null,
            'task_status' => 'submitted',
            'updated_at' => now(),
        ];
        
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
        
        DB::table('intern_tasks')
            ->where('task_id', $id)
            ->update($updateData);
        
        // Create notification for intern
        $this->createSubmissionNotification($task, $intern);
        
      return redirect()->route('intern.tasks')
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