<?php

namespace App\Http\Controllers\supervisor_controllers;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\InternAccount;
use App\Models\ManagersAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    /**
     * Display a listing of tasks (Supervisor view)
     */
    public function index(Request $request)
    {
        $supervisor = Auth::guard('manager')->user();
        
        // REMOVED 'project' from with() - Fixes the error
        $query = Task::where('supervisor_id', $supervisor->manager_id)
                     ->with(['intern']);  // Only load intern relationship

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

        $tasks = $query->orderBy('created_at', 'desc')
                       ->paginate(15);

        // Get interns for filter
        $interns = InternAccount::whereIn('int_status', ['Active', 'Test'])->get();

        return view('pages.supervisor.tasks.index', compact('tasks', 'interns'));
    }

    /**
     * Show form to create task
     */
    public function create()
    {
        $supervisor = Auth::guard('manager')->user();
        
        // Get interns assigned to this supervisor
        $interns = InternAccount::where('supervisor_id', $supervisor->manager_id)
                                 ->whereIn('int_status', ['Active', 'Test'])
                                 ->get();
        
        return view('pages.supervisor.tasks.create', compact('interns'));
    }

    /**
     * Store new task
     */
    public function store(Request $request)
    {
        $supervisor = Auth::guard('manager')->user();
        
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'intern_id' => 'required|exists:intern_accounts,int_id',
            'deadline' => 'required|date|after:today',
            'points' => 'required|integer|min:1|max:100'
        ]);

        try {
            DB::beginTransaction();

            $task = Task::create([
                'title' => $request->title,
                'description' => $request->description,
                'supervisor_id' => $supervisor->manager_id,
                'intern_id' => $request->intern_id,
                'deadline' => $request->deadline,
                'points' => $request->points,
                'status' => 'pending'
            ]);

            // Log activity
            DB::table('audit_logs')->insert([
                'user_id' => $supervisor->manager_id,
                'user_type' => 'supervisor',
                'action' => 'task_created',
                'details' => "Task '{$task->title}' created for intern ID: {$request->intern_id}",
                'ip_address' => $request->ip(),
                'created_at' => now()
            ]);

            DB::commit();

            return redirect()->route('supervisor.tasks.index')
                ->with('success', 'Task created successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create task: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show task details
     */
    public function show($id)
    {
        $supervisor = Auth::guard('manager')->user();
        // REMOVED 'project' from with()
        $task = Task::where('supervisor_id', $supervisor->manager_id)
                    ->with(['intern'])  // Only load intern
                    ->findOrFail($id);
        
        return view('pages.supervisor.tasks.show', compact('task'));
    }

    /**
     * Show edit form
     */
    public function edit($id)
    {
        $supervisor = Auth::guard('manager')->user();
        $task = Task::where('supervisor_id', $supervisor->manager_id)->findOrFail($id);
        
        $interns = InternAccount::where('supervisor_id', $supervisor->manager_id)
                                 ->whereIn('int_status', ['Active', 'Test'])
                                 ->get();
        
        return view('pages.supervisor.tasks.edit', compact('task', 'interns'));
    }

    /**
     * Update task
     */
    public function update(Request $request, $id)
    {
        $supervisor = Auth::guard('manager')->user();
        $task = Task::where('supervisor_id', $supervisor->manager_id)->findOrFail($id);
        
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'intern_id' => 'required|exists:intern_accounts,int_id',
            'deadline' => 'required|date',
            'points' => 'required|integer|min:1|max:100'
        ]);

        try {
            DB::beginTransaction();

            $task->update($request->all());

            // Log activity
            DB::table('audit_logs')->insert([
                'user_id' => $supervisor->manager_id,
                'user_type' => 'supervisor',
                'action' => 'task_updated',
                'details' => "Task '{$task->title}' (ID: {$task->id}) updated",
                'ip_address' => $request->ip(),
                'created_at' => now()
            ]);

            DB::commit();

            return redirect()->route('supervisor.tasks.index')
                ->with('success', 'Task updated successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update task: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Delete task
     */
    public function destroy(Request $request, $id)
    {
        $supervisor = Auth::guard('manager')->user();
        $task = Task::where('supervisor_id', $supervisor->manager_id)->findOrFail($id);
        
        try {
            DB::beginTransaction();

            $taskTitle = $task->title;
            $task->delete();

            // Log activity
            DB::table('audit_logs')->insert([
                'user_id' => $supervisor->manager_id,
                'user_type' => 'supervisor',
                'action' => 'task_deleted',
                'details' => "Task '{$taskTitle}' deleted",
                'ip_address' => $request->ip(),
                'created_at' => now()
            ]);

            DB::commit();

            return redirect()->route('supervisor.tasks.index')
                ->with('success', 'Task deleted successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete task: ' . $e->getMessage());
        }
    }

    /**
     * Grade submitted task
     */
    public function grade(Request $request, $id)
    {
        $supervisor = Auth::guard('manager')->user();
        $task = Task::where('supervisor_id', $supervisor->manager_id)
                    ->where('status', 'submitted')
                    ->findOrFail($id);
        
        $request->validate([
            'grade' => 'required|integer|min:0|max:' . $task->points,
            'supervisor_remarks' => 'nullable|string|max:500'
        ]);

        try {
            DB::beginTransaction();

            $task->grade = $request->grade;
            $task->supervisor_remarks = $request->supervisor_remarks;
            $task->status = $request->grade >= ($task->points * 0.5) ? 'approved' : 'rejected';
            $task->reviewed_at = now();
            $task->save();

            DB::commit();

            return redirect()->route('supervisor.tasks.show', $task->id)
                ->with('success', 'Task graded successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to grade task: ' . $e->getMessage());
        }
    }
}