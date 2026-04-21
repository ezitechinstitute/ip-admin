<?php

/**
 * ============================================
 * NOTIFICATION SYSTEM UPDATE - PHASE 5
 * ============================================
 * Date: 2026-04-18
 * 
 * PURPOSE: Jab intern ka end date aa jaye
 * TRIGGER: When intern reaches end date
 * RECIPIENT: Manager
 * ============================================
 */

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class InternshipCompletionNotification extends Notification implements ShouldQueue
{
    use Queueable;
    
    protected $intern;
    
    public function __construct($intern)
    {
        $this->intern = $intern;
    }
    
    public function via($notifiable)
    {
        return ['mail'];
    }
    
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject("Internship Completion Review Required: {$this->intern->name}")
            ->greeting("Hi {$notifiable->name},")
            ->line("Intern **{$this->intern->name}** has reached their official end date.")
            ->line("Status has been set to **Completed (Pending Approval)**.")
            ->line("**Intern Name:** {$this->intern->name}")
            ->line("**Technology:** {$this->intern->int_technology}")
            ->line("**End Date:** {$this->intern->end_date}")
            ->line("Please review their performance and approve or reject the completion.")
            ->action('Review & Approve', url("/manager/interns/{$this->intern->int_id}/completion"))
            ->line('Once approved, the intern can request their certificate.');
    }
}