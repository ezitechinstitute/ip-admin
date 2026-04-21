<?php

/**
 * ============================================
 * NOTIFICATION SYSTEM UPDATE - PHASE 5
 * ============================================
 * Date: 2026-04-18
 * 
 * PURPOSE: Jab intern ne certificate request kiya
 * TRIGGER: When intern requests a certificate
 * RECIPIENT: Manager
 * ============================================
 */

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class CertificateRequestedNotification extends Notification implements ShouldQueue
{
    use Queueable;
    
    protected $intern;
    protected $certificateType;
    protected $requestId;
    
    public function __construct($intern, string $certificateType, $requestId)
    {
        $this->intern = $intern;
        $this->certificateType = $certificateType;
        $this->requestId = $requestId;
    }
    
    public function via($notifiable)
    {
        return ['mail'];
    }
    
    public function toMail($notifiable)
    {
        $typeLabel = str_replace('_', ' ', ucfirst($this->certificateType));
        
        return (new MailMessage)
            ->subject("Certificate Request: {$this->intern->name}")
            ->greeting("Hi {$notifiable->name},")
            ->line("Intern **{$this->intern->name}** has requested a certificate.")
            ->line("**Certificate Type:** {$typeLabel}")
            ->line("**Intern Name:** {$this->intern->name}")
            ->line("**Technology:** {$this->intern->int_technology}")
            ->action('Review Request', url("/manager/certificates/{$this->requestId}"))
            ->line('Please review the request and approve or reject accordingly.');
    }
}