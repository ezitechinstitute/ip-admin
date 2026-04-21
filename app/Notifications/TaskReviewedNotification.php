<?php

/**
 * ============================================
 * NOTIFICATION SYSTEM UPDATE - PHASE 5
 * ============================================
 * Date: 2026-04-18
 * 
 * PURPOSE: Jab supervisor ne task approve/reject kiya
 * TRIGGER: When supervisor approves/rejects a task
 * RECIPIENT: Intern
 * ============================================
 */

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class TaskReviewedNotification extends Notification implements ShouldQueue
{
    use Queueable;
    
    protected $task;
    protected $status;
    protected $remarks;
    
    public function __construct($task, string $status, string $remarks = '')
    {
        $this->task = $task;
        $this->status = $status;
        $this->remarks = $remarks;
    }
    
    public function via($notifiable)
    {
        return ['mail'];
    }
    
    public function toMail($notifiable)
    {
        $statusEmoji = match($this->status) {
            'approved' => '✅',
            'rejected' => '❌',
            default => '🔄',
        };
        
        $statusText = ucfirst($this->status);
        
        $mail = (new MailMessage)
            ->subject("{$statusEmoji} Task {$statusText}: {$this->task->task_title}")
            ->greeting("Hi {$notifiable->name},")
            ->line("Your task submission has been reviewed.")
            ->line("**Task:** {$this->task->task_title}")
            ->line("**Status:** {$statusText}");
        
        if ($this->remarks) {
            $mail->line("**Remarks:** {$this->remarks}");
        }
        
        if ($this->status === 'approved') {
            $mail->line("**Points Obtained:** {$this->task->task_obt_points}/{$this->task->task_points}");
        }
        
        if ($this->status === 'rejected') {
            $mail->line("Please review the feedback and resubmit the task.");
        }
        
        return $mail->action('View Task', url("/intern/tasks/{$this->task->task_id}"));
    }
}