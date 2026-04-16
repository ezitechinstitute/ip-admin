<?php

namespace App\Services;

use App\Models\Task;
use App\Models\InternTask;
use App\Models\ProjectTask;
use App\Models\InternAccount;
use App\Models\ManagersAccount;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TaskManagementService
{
    /**
     * Get all tasks for an intern across all task types
     * Returns unified task structure
     */
    public function getInternTasks($internId, $filters = [])
    {
        try {
            $tasks = [];

            // Get general tasks
            $generalTasks = Task::where('intern_id', $internId)
                ->when(isset($filters['status']), function($q) use ($filters) {
                    return $q->where('status', $filters['status']);
                })
                ->when(isset($filters['project_id']), function($q) use ($filters) {
                    return $q->where('project_id', $filters['project_id']);
                })
                ->get()
                ->map(fn($t) => $this->formatTask($t, 'general'))
                ->toArray();

            // Get intern tasks (via eti_id)
            $intern = InternAccount::where('int_id', $internId)->first();
            if ($intern) {
                $internSpecificTasks = InternTask::where('eti_id', $intern->eti_id)
                    ->when(isset($filters['status']), function($q) use ($filters) {
                        return $q->where('task_status', $filters['status']);
                    })
                    ->get()
                    ->map(fn($t) => $this->formatTask($t, 'intern'))
                    ->toArray();
                $tasks = array_merge($tasks, $internSpecificTasks);
            }

            // Get project tasks (via eti_id)
            if ($intern) {
                $projectTasks = ProjectTask::where('eti_id', $intern->eti_id)
                    ->when(isset($filters['status']), function($q) use ($filters) {
                        return $q->where('task_status', $filters['status']);
                    })
                    ->when(isset($filters['project_id']), function($q) use ($filters) {
                        return $q->where('project_id', $filters['project_id']);
                    })
                    ->get()
                    ->map(fn($t) => $this->formatTask($t, 'project'))
                    ->toArray();
                $tasks = array_merge($tasks, $projectTasks);
            }

            return collect($tasks);
        } catch (\Exception $e) {
            Log::error("Error getting intern tasks: {$e->getMessage()}");
            return collect([]);
        }
    }

    /**
     * Get all tasks for a manager/supervisor
     */
    public function getManagerTasks($managerId, $filters = [])
    {
        try {
            $tasks = [];

            // General tasks assigned by this manager
            $generalTasks = Task::where('supervisor_id', $managerId)
                ->when(isset($filters['status']), function($q) use ($filters) {
                    return $q->where('status', $filters['status']);
                })
                ->get()
                ->map(fn($t) => $this->formatTask($t, 'general'))
                ->toArray();
            $tasks = array_merge($tasks, $generalTasks);

            // Intern tasks assigned by this manager
            $internTasks = InternTask::where('assigned_by', $managerId)
                ->when(isset($filters['status']), function($q) use ($filters) {
                    return $q->where('task_status', $filters['status']);
                })
                ->get()
                ->map(fn($t) => $this->formatTask($t, 'intern'))
                ->toArray();
            $tasks = array_merge($tasks, $internTasks);

            // Project tasks assigned by this manager
            $projectTasks = ProjectTask::where('assigned_by', $managerId)
                ->when(isset($filters['status']), function($q) use ($filters) {
                    return $q->where('task_status', $filters['status']);
                })
                ->get()
                ->map(fn($t) => $this->formatTask($t, 'project'))
                ->toArray();
            $tasks = array_merge($tasks, $projectTasks);

            return collect($tasks);
        } catch (\Exception $e) {
            Log::error("Error getting manager tasks: {$e->getMessage()}");
            return collect([]);
        }
    }

    /**
     * Get pending/overdue tasks
     */
    public function getOverdueTasks($days = 0)
    {
        try {
            $cutoffDate = now()->subDays($days);
            $tasks = [];

            // General overdue tasks
            $generalOverdue = Task::where('deadline', '<', $cutoffDate)
                ->whereIn('status', ['pending', 'submitted'])
                ->get()
                ->map(fn($t) => $this->formatTask($t, 'general'))
                ->toArray();
            $tasks = array_merge($tasks, $generalOverdue);

            // Intern overdue tasks
            $internOverdue = InternTask::whereDate('task_end', '<', $cutoffDate)
                ->whereIn('task_status', ['pending', 'submitted'])
                ->get()
                ->map(fn($t) => $this->formatTask($t, 'intern'))
                ->toArray();
            $tasks = array_merge($tasks, $internOverdue);

            // Project overdue tasks
            $projectOverdue = ProjectTask::whereDate('t_end_date', '<', $cutoffDate)
                ->whereIn('task_status', ['pending', 'submitted'])
                ->get()
                ->map(fn($t) => $this->formatTask($t, 'project'))
                ->toArray();
            $tasks = array_merge($tasks, $projectOverdue);

            return collect($tasks)->sortBy('deadline');
        } catch (\Exception $e) {
            Log::error("Error getting overdue tasks: {$e->getMessage()}");
            return collect([]);
        }
    }

    /**
     * Get task statistics
     */
    public function getTaskStatistics($managerId = null)
    {
        try {
            $general = Task::when($managerId, function($q) use ($managerId) {
                return $q->where('supervisor_id', $managerId);
            })->count();

            $intern = InternTask::when($managerId, function($q) use ($managerId) {
                return $q->where('assigned_by', $managerId);
            })->count();

            $project = ProjectTask::when($managerId, function($q) use ($managerId) {
                return $q->where('assigned_by', $managerId);
            })->count();

            return [
                'general_tasks' => $general,
                'intern_tasks' => $intern,
                'project_tasks' => $project,
                'total' => $general + $intern + $project,
            ];
        } catch (\Exception $e) {
            Log::error("Error getting task statistics: {$e->getMessage()}");
            return ['general_tasks' => 0, 'intern_tasks' => 0, 'project_tasks' => 0, 'total' => 0];
        }
    }

    /**
     * Format task to unified structure
     */
    private function formatTask($task, $type): array
    {
        $formatted = [
            'id' => $task->id ?? $task->task_id ?? null,
            'task_type' => $type,
            'title' => $task->title ?? $task->task_title ?? 'Untitled',
            'description' => $task->description ?? $task->task_description ?? null,
            'status' => $task->status ?? $task->task_status ?? 'pending',
            'points' => $task->points ?? $task->task_points ?? $task->task_mark ?? 0,
            'obtained_points' => $task->task_obt_points ?? $task->task_obt_mark ?? 0,
            'deadline' => $this->getDeadline($task, $type),
            'supervisor_remarks' => $task->supervisor_remarks ?? $task->review ?? null,
            'submission_notes' => $task->submission_notes ?? $task->submit_description ?? null,
            'grade' => $task->grade ?? null,
            'submitted_at' => $task->submitted_at ?? null,
            'reviewed_at' => $task->reviewed_at ?? null,
        ];

        return array_filter($formatted, fn($v) => $v !== null);
    }

    /**
     * Get deadline from different task types
     */
    private function getDeadline($task, $type)
    {
        return match ($type) {
            'general' => $task->deadline ?? null,
            'intern' => Carbon::createFromFormat('Y-m-d', $task->task_end) ?? null,
            'project' => $task->t_end_date ?? null,
            default => null,
        };
    }

    /**
     * Update task status across all types
     */
    public function updateTaskStatus($taskId, $status, $type = 'general')
    {
        try {
            if ($type === 'general') {
                return Task::find($taskId)?->update(['status' => $status]);
            } elseif ($type === 'intern') {
                return InternTask::where('task_id', $taskId)?->update(['task_status' => $status]);
            } elseif ($type === 'project') {
                return ProjectTask::where('task_id', $taskId)?->update(['task_status' => $status]);
            }
            return false;
        } catch (\Exception $e) {
            Log::error("Error updating task status: {$e->getMessage()}");
            return false;
        }
    }
}
