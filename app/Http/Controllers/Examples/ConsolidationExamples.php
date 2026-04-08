<?php

/**
 * EXAMPLE: Refactored InternTaskController using TaskManagementService
 * 
 * This file demonstrates how to migrate existing controllers to use the
 * TaskManagementService for consolidated task management.
 * 
 * Location: app/Http/Controllers/Examples/InternTaskControllerExample.php
 */

namespace App\Http\Controllers\Examples;

use App\Http\Controllers\Controller;
use App\Services\TaskManagementService;
use App\Models\InternAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class InternTaskControllerExample extends Controller
{
    protected $taskService;

    public function __construct(TaskManagementService $taskService)
    {
        $this->taskService = $taskService;
    }

    /**
     * Display all tasks for logged-in intern
     * 
     * This now retrieves tasks from ALL three systems
     * (general tasks, intern tasks, project tasks) unified
     */
    public function index(Request $request)
    {
        try {
            // Get authenticated intern
            $intern = auth('intern')->user(); // InternAccount
            if (!$intern) {
                return redirect()->route('login');
            }

            // Get filtered tasks from all systems
            $filters = [];
            if ($request->filled('status')) {
                $filters['status'] = $request->status;
            }
            if ($request->filled('project_id')) {
                $filters['project_id'] = $request->project_id;
            }

            $tasks = $this->taskService->getInternTasks($intern->int_id, $filters);

            // Get statistics
            $stats = $this->taskService->getTaskStatistics();

            return view('intern.tasks.index', [
                'tasks' => $tasks,
                'stats' => $stats,
                'filters' => $filters,
            ]);

        } catch (\Exception $e) {
            Log::error("Error listing tasks: {$e->getMessage()}");
            return redirect()->back()->with('error', 'Failed to load tasks');
        }
    }

    /**
     * Show task details
     * 
     * Works with any task type since service abstracts the type
     */
    public function show($taskId)
    {
        try {
            $intern = auth('intern')->user();
            
            // Get task from appropriate model (service handles)
            // In real impl, would need to know task_type or query all
            $tasks = $this->taskService->getInternTasks($intern->int_id);
            $task = $tasks->firstWhere('id', $taskId);

            if (!$task) {
                return redirect()->route('intern.tasks.index')
                    ->with('error', 'Task not found');
            }

            return view('intern.tasks.show', ['task' => $task]);

        } catch (\Exception $e) {
            Log::error("Error showing task: {$e->getMessage()}");
            return redirect()->back()->with('error', 'Failed to load task');
        }
    }

    /**
     * Submit a task with solution details
     */
    public function submit(Request $request, $taskId)
    {
        try {
            $validated = $request->validate([
                'submission_notes' => 'required|string|min:10',
                'task_screenshot' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'task_live_url' => 'nullable|url',
                'task_git_url' => 'nullable|url',
            ]);

            $intern = auth('intern')->user();

            // Get the task (simplified - would need to query all types)
            $tasks = $this->taskService->getInternTasks($intern->int_id);
            $task = $tasks->firstWhere('id', $taskId);

            if (!$task) {
                return response()->json(['error' => 'Task not found'], 404);
            }

            // Update based on task type (service handles this)
            $success = $this->taskService->updateTaskStatus(
                $taskId,
                'submitted',
                $task['task_type']
            );

            if ($success) {
                Log::info("Task submitted", [
                    'task_id' => $taskId,
                    'intern_id' => $intern->int_id,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Task submitted successfully',
                ]);
            }

            return response()->json(['error' => 'Failed to submit task'], 500);

        } catch (\Exception $e) {
            Log::error("Error submitting task: {$e->getMessage()}");
            return response()->json(['error' => 'Submission failed'], 500);
        }
    }

    /**
     * Get task statistics for dashboard
     */
    public function statistics()
    {
        try {
            $stats = $this->taskService->getTaskStatistics();
            $overdue = $this->taskService->getOverdueTasks();

            return response()->json([
                'statistics' => $stats,
                'overdue_count' => $overdue->count(),
                'overdue_tasks' => $overdue->take(5)->values(),
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch statistics'], 500);
        }
    }
}

// ============================================
// EXAMPLE: Refactored ManagerLeaveController using LeaveManagementService
// ============================================

class ManagerLeaveControllerExample extends Controller
{
    protected $leaveService;

    public function __construct()
    {
        $this->leaveService = new \App\Services\LeaveManagementService();
    }

    /**
     * Display pending leave requests for manager approval
     */
    public function index(Request $request)
    {
        try {
            $manager = auth('manager')->user();

            // Get all pending leaves (both intern and supervisor)
            $pendingLeaves = $this->leaveService->getPendingLeaves();

            // Optional: Filter by type
            if ($request->filled('type')) {
                $pendingLeaves = $this->leaveService->getPendingLeaves($request->type);
            }

            // Get statistics
            $stats = $this->leaveService->getLeaveStatistics();

            return view('manager.leaves.index', [
                'pending' => $pendingLeaves,
                'statistics' => $stats,
            ]);

        } catch (\Exception $e) {
            Log::error("Error listing leaves: {$e->getMessage()}");
            return redirect()->back()->with('error', 'Failed to load leaves');
        }
    }

    /**
     * Approve a leave request
     * 
     * Works with any leave type (intern, supervisor, employee)
     */
    public function approve(Request $request, $leaveId, $type)
    {
        try {
            $validated = $request->validate([
                'notes' => 'nullable|string|max:500',
            ]);

            // Approve using service (type-agnostic)
            $success = $this->leaveService->approveLeave($leaveId, $type, $validated['notes'] ?? null);

            if ($success) {
                return response()->json([
                    'success' => true,
                    'message' => ucfirst($type) . ' leave approved successfully',
                ]);
            }

            return response()->json(['error' => 'Approval failed'], 500);

        } catch (\Exception $e) {
            Log::error("Error approving leave: {$e->getMessage()}");
            return response()->json(['error' => 'Failed to approve leave'], 500);
        }
    }

    /**
     * Reject a leave request
     */
    public function reject(Request $request, $leaveId, $type)
    {
        try {
            $validated = $request->validate([
                'reason' => 'required|string|min:10|max:500',
            ]);

            $success = $this->leaveService->rejectLeave($leaveId, $type, $validated['reason']);

            if ($success) {
                return response()->json([
                    'success' => true,
                    'message' => 'Leave request rejected',
                ]);
            }

            return response()->json(['error' => 'Rejection failed'], 500);

        } catch (\Exception $e) {
            Log::error("Error rejecting leave: {$e->getMessage()}");
            return response()->json(['error' => 'Failed to reject leave'], 500);
        }
    }

    /**
     * Get active leaves (currently on leave)
     */
    public function activeLeaves()
    {
        try {
            $active = $this->leaveService->getActiveLeaves();

            return response()->json([
                'active_leaves' => $active,
                'count' => $active->count(),
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch active leaves'], 500);
        }
    }

    /**
     * Get leave statistics
     */
    public function statistics()
    {
        try {
            $stats = $this->leaveService->getLeaveStatistics();

            return response()->json([
                'statistics' => $stats,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch statistics'], 500);
        }
    }
}

// ============================================
// MIGRATION STEPS
// ============================================

/**
 * To migrate an existing controller:
 * 
 * 1. Inject the service in __construct:
 *    protected $service;
 *    public function __construct(Service $service) {
 *        $this->service = $service;
 *    }
 * 
 * 2. Replace direct model queries with service calls:
 *    OLD: $tasks = InternTask::where('eti_id', $eti_id)->get();
 *    NEW: $tasks = $this->service->getInternTasks($int_id);
 * 
 * 3. Use unified return format:
 *    $task['task_type']    // 'general'|'intern'|'project'
 *    $task['status']       // 'pending'|'submitted'|'approved'|'rejected'
 * 
 * 4. Leverage helper methods on models:
 *    $task->isPending()
 *    $task->approve()
 *    $task->getDaysUntilDeadline()
 * 
 * 5. Test thoroughly before deploying
 */
