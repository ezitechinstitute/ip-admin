<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class TaskService
{
    /**
     * Get tasks with pagination
     */
    public function getTasksWithPagination(string $etiId, int $perPage = 10)
    {
        return DB::table('intern_tasks')
            ->where('eti_id', $etiId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get all tasks (unpaginated) for Kanban/Timeline views
     */
    public function getAllTasks(string $etiId)
    {
        return DB::table('intern_tasks')
            ->where('eti_id', $etiId)
            ->orderBy('created_at', 'desc')
            ->get();
    }
    
    /**
     * Get task statistics
     */
    public function getTaskStatistics(string $etiId): array
    {
        return [
            'total' => DB::table('intern_tasks')->where('eti_id', $etiId)->count(),
            'pending' => DB::table('intern_tasks')->where('eti_id', $etiId)->whereIn('task_status', ['pending', 'Assigned'])->count(),
            'submitted' => DB::table('intern_tasks')->where('eti_id', $etiId)->where('task_status', 'submitted')->count(),
            'approved' => DB::table('intern_tasks')->where('eti_id', $etiId)->whereIn('task_status', ['Completed', 'approved'])->count(),
            'rejected' => DB::table('intern_tasks')->where('eti_id', $etiId)->where('task_status', 'Rejected')->count(),
        ];
    }
    
    /**
     * Get single task by ID
     */
    public function getTaskById(string $taskId, string $etiId)
    {
        return DB::table('intern_tasks')
            ->where('task_id', $taskId)
            ->where('eti_id', $etiId)
            ->first();
    }
    
    /**
     * Update task submission
     */
    public function updateTaskSubmission(string $taskId, array $data): bool
    {
        return DB::table('intern_tasks')
            ->where('task_id', $taskId)
            ->update($data);
    }
    
    /**
     * Upload screenshot
     */
    public function uploadScreenshot($file, int $internId, string $taskId): string
    {
        $fileName = time() . '_' . $internId . '_' . $taskId . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('uploads/task_submissions', $fileName, 'public');
        return 'storage/' . $path;
    }
    
    /**
     * Delete old screenshot
     */
    public function deleteOldScreenshot(?string $screenshotPath): void
    {
        if ($screenshotPath && $screenshotPath != 'NULL') {
            $oldPath = str_replace('storage/', '', $screenshotPath);
            if (Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }
        }
    }
    
    /**
     * Create notification
     */
    public function createNotification(int $internId, string $taskTitle): void
    {
        if (!Schema::hasTable('intern_notifications')) {
            return;
        }
        
        DB::table('intern_notifications')->insert([
            'intern_id' => $internId,
            'title' => 'Task Submitted',
            'message' => 'You have successfully submitted "' . $taskTitle . '". Please wait for supervisor review.',
            'type' => 'task',
            'is_read' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
    
    /**
     * Check if task can be submitted
     */
    public function canSubmitTask(string $taskStatus): bool
    {
        return !in_array($taskStatus, ['Completed', 'approved']);
    }
    
    /**
     * Check if task can be resubmitted
     */
    public function canResubmitTask(string $taskStatus): bool
    {
        return in_array($taskStatus, ['Rejected', 'Assigned', 'pending']);
    }
}