<?php

/**
 * ============================================
 * NOTIFICATION SYSTEM UPDATE - PHASE 5
 * ============================================
 * Date: 2026-04-18
 * 
 * PURPOSE: Jab task ki deadline guzar jaye
 * TRIGGER: When task deadline passes
 * RECIPIENT: Intern, Supervisor, Manager (all three)
 * ============================================
 */

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class TaskExpiredNotification extends Notification implements ShouldQueue
{
    use Queueable;
    
    protected $task;
    protected $intern;
    protected $recipientRole;
    
    public function __construct($task, $intern, string $recipientRole)
    {
        $this->task = $task;
        $this->intern = $intern;
        $this->recipientRole = $recipientRole;
    }
    
    public function via($notifiable)
    {
        return ['mail'];
    }
    
    public function toMail($notifiable)
    {
        $message = $this->recipientRole === 'intern'
            ? "Your task **{$this->task->task_title}** has expired without submission."
            : "Task **{$this->task->task_title}** for intern **{$this->intern->name}** has expired.";
        
        $url = $this->recipientRole === 'intern'
            ? url("/intern/tasks/{$this->task->task_id}")
            : url("/{$this->recipientRole}/tasks/{$this->task->task_id}");
        
        return (new MailMessage)
            ->subject("⚠️ Task Expired: {$this->task->task_title}")
            ->greeting("Hi {$notifiable->name},")
            ->line($message)
            ->line("**Task Title:** {$this->task->task_title}")
            ->line("**Deadline was:** " . ($this->task->task_end ?? 'N/A'))
            ->action('View Task Details', $url)
            ->line('Please take appropriate action.');
    }
}