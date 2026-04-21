<?php

/**
 * ============================================
 * NOTIFICATION SYSTEM UPDATE - PHASE 5
 * ============================================
 * Date: 2026-04-18
 * 
 * PURPOSE: Jab kisi manager/supervisor ko naya intern assign ho
 * TRIGGER: When intern is assigned to manager or supervisor
 * RECIPIENT: Manager or Supervisor
 * ============================================
 */

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class NewInternAssignedNotification extends Notification implements ShouldQueue
{
    use Queueable;
    
    protected $intern;
    protected $recipientRole;
    
    public function __construct($intern, string $recipientRole = 'manager')
    {
        $this->intern = $intern;
        $this->recipientRole = $recipientRole;
    }
    
    public function via($notifiable)
    {
        return ['mail'];  // Only email, no database
    }
    
    public function toMail($notifiable)
    {
        $roleLabel = ucfirst($this->recipientRole);
        $url = $this->recipientRole === 'supervisor' 
            ? url("/supervisor/interns/{$this->intern->int_id}")
            : url("/manager/interns/{$this->intern->int_id}");
        
        return (new MailMessage)
            ->subject("New Intern Assigned: {$this->intern->name}")
            ->greeting("Hi {$notifiable->name},")
            ->line("A new intern has been assigned to you.")
            ->line("**Name:** {$this->intern->name}")
            ->line("**Email:** {$this->intern->email}")
            ->line("**Technology:** {$this->intern->int_technology}")
            ->line("**Join Date:** {$this->intern->start_date}")
            ->action('View Intern Profile', $url)
            ->line('Please review the intern profile and assign appropriate tasks.');
    }
}