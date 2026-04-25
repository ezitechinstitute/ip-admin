<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class InternDashboardService
{
    public function prepareDashboardData($intern, array $stats, array $progress, array $performance, Collection $recentTasks, Collection $upcomingDeadlines, Collection $notifications, Collection $timeline, ?string $freezeWarning): object
    {
        $data = new \stdClass();

        // Basic intern info
        $data->internName = $intern->name ?? 'Intern';
        $data->internshipStatus = $stats['internship_status'] ?? 'Active';
        $data->freezeWarning = $freezeWarning;

        // Progress & dates
        $startDate = $intern->start_date ? Carbon::parse($intern->start_date) : Carbon::now();
        $endDate = $startDate->copy()->addMonths(6);
        $totalDays = $startDate->diffInDays($endDate);
        $elapsedDays = $startDate->diffInDays(Carbon::now());
        $data->progressPercent = $totalDays > 0 ? min(100, round(($elapsedDays / $totalDays) * 100)) : 0;

        if ($data->progressPercent > 70) $data->motivationMessage = "You're doing great 🚀";
        elseif ($data->progressPercent > 40) $data->motivationMessage = "Keep going 💪";
        else $data->motivationMessage = "Needs attention ⚠️";

        $data->remainingDays = $stats['remaining_days'] ?? 0;

        // Next deadline
        $firstDeadline = $upcomingDeadlines->first();
        if ($firstDeadline) {
            $data->nextDeadline = (object)[
                'formatted_date' => Carbon::parse($firstDeadline->task_end)->format('d M Y'),
                'days_left' => max(0, Carbon::now()->diffInDays(Carbon::parse($firstDeadline->task_end), false))
            ];
        } else {
            $data->nextDeadline = null;
        }

        // Action required collections & safe fields
        $now = Carbon::now();
        $overdueTasks = $recentTasks->filter(fn($t) => Carbon::parse($t->task_end)->isPast() && $t->task_status !== 'approved');
        $deadlineSoon = $recentTasks->filter(fn($t) => ($diff = $now->diffInDays(Carbon::parse($t->task_end), false)) <= 3 && $diff >= 0 && $t->task_status !== 'approved');
        $pendingSubmissions = $recentTasks->filter(fn($t) => $t->task_status === 'pending');

        $data->hasOverdueTasks = $overdueTasks->isNotEmpty();
        $data->overdueTasksCount = $overdueTasks->count();
        $data->overdueTaskTitles = $overdueTasks->take(2)->pluck('task_title');

        $data->hasDeadlineSoon = $deadlineSoon->isNotEmpty();
        $data->deadlineSoonCount = $deadlineSoon->count();
        $data->deadlineSoonTitles = $deadlineSoon->take(2)->pluck('task_title');

        $data->hasPendingSubmissions = $pendingSubmissions->isNotEmpty();
        $data->pendingSubmissionsCount = $pendingSubmissions->count();
        $data->pendingSubmissionTitles = $pendingSubmissions->take(2)->pluck('task_title');

        $data->totalActionCount = $overdueTasks->count() + $deadlineSoon->count() + $pendingSubmissions->count();
        $data->hasActionRequired = $data->totalActionCount > 0;

        // Stat cards
        $data->tasksCompleted = $stats['tasks_completed'] ?? 0;
        $data->totalTasks = $stats['tasks_total'] ?? 0;
        $data->completionRate = $data->totalTasks > 0 ? round(($data->tasksCompleted / $data->totalTasks) * 100) : 0;
        $data->averageScore = round($performance['average_score'] ?? 0);
        $data->activeProjects = $stats['projects_ongoing'] ?? 0;
        $data->totalProjects = $stats['projects_assigned'] ?? 0;
        $data->projectCompletionRate = $data->totalProjects > 0 ? round(($stats['projects_completed'] / $data->totalProjects) * 100) : 0;

        $data->taskProgressPercent = $progress['task_percentage'] ?? 0;
        $data->projectProgressPercent = $progress['project_percentage'] ?? 0;

        // Recent tasks (safe fields)
        $data->recentTasks = $recentTasks->map(function($task) {
            $priority = strtolower($task->task_priority ?? 'medium');
            $task->priority_color = match($priority) {
                'high' => 'danger',
                'low' => 'success',
                default => 'warning'
            };
            $task->priority_icon = match($priority) {
                'high' => 'arrow-up',
                'low' => 'arrow-down',
                default => 'dash'
            };
            $task->priority_label = ucfirst($priority);

            $status = strtolower(str_replace(' ', '', $task->task_status));
            $task->status_color = match($status) {
                'approved' => 'success',
                'submitted' => 'primary',
                'pending' => 'warning',
                'overdue' => 'danger',
                default => 'secondary'
            };
            $task->status_icon = match($status) {
                'approved' => 'check-circle',
                'submitted' => 'send',
                default => 'clock'
            };
            $task->status_label = ucfirst($task->task_status);

            $task->formatted_deadline = Carbon::parse($task->task_end)->format('d M Y');
            return $task;
        });

        // Notifications
        $data->notifications = $notifications->map(function($notif) {
            $notif->time_ago = Carbon::parse($notif->created_at)->diffForHumans();
            return $notif;
        });

        // Timeline
        $data->timeline = $timeline->map(function($event) {
            $event = (object)$event;
            $event->bg_color = match($event->color) {
                'success' => '#e8f5e9',
                'danger' => '#ffebee',
                default => '#e3f2fd'
            };
            $event->icon = match($event->icon) {
                'ti ti-rocket' => 'bi-rocket-takeoff',
                'ti ti-briefcase' => 'bi-briefcase',
                default => 'bi-flag'
            };
            return $event;
        });

        // Chart data – ensure arrays, use actual task completion or fallback to zeros
        $taskCompletionData = $performance['task_completion'] ?? collect();
        $data->chartWeekLabels = [];
        $data->chartTaskCompletion = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $data->chartWeekLabels[] = $date->format('D');
            $data->chartTaskCompletion[] = 0;
        }

        foreach ($taskCompletionData as $item) {
            $dayName = Carbon::parse($item->date)->format('D');
            $idx = array_search($dayName, $data->chartWeekLabels);
            if ($idx !== false) {
                $data->chartTaskCompletion[$idx] = (int) $item->count;
            }
        }

        // Performance trend data
        $data->chartPerformanceTrend = [72, 68, 74, 79, 82, 85, $data->averageScore];

        // Debug (remove after confirming charts work) - 
        // Log::info('Chart data prepared', [
        //     'labels' => $data->chartWeekLabels,
        //     'tasks' => $data->chartTaskCompletion,
        //     'performance' => $data->chartPerformanceTrend
        // ]);

        return $data;
    }
}