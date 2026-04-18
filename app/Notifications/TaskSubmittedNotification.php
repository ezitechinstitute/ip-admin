<?php

/**
 * ============================================
 * NOTIFICATION SYSTEM UPDATE - PHASE 5
 * ============================================
 * Date: 2026-04-18
 * 
 * PURPOSE: Jab intern ne task submit kiya
 * TRIGGER: When intern submits a task
 * RECIPIENT: Supervisor
 * ============================================
 */

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class TaskSubmittedNotification extends Notification implements ShouldQueue
{
    use Queueable;
    
    protected $task;
    protected $intern;
    
    public function __construct($task, $intern)
    {
        $this->task = $task;
        $this->intern = $intern;
    }
    
    public function via($notifiable)
    {
        return ['mail'];
    }
    
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject("Task Submitted: {$this->task->task_title}")
            ->greeting("Hi {$notifiable->name},")
            ->line("Intern **{$this->intern->name}** has submitted a task for your review.")
            ->line("**Task:** {$this->task->task_title}")
            ->line("**Submitted at:** " . now()->format('d M Y, h:i A'))
            ->action('Review Task', url("/supervisor/tasks/{$this->task->task_id}"))
            ->line('Please review and provide feedback at your earliest convenience.');
    }
}