<?php

/**
 * ============================================
 * NOTIFICATION SYSTEM UPDATE - PHASE 5
 * ============================================
 * Date: 2026-04-18
 * 
 * PURPOSE: Jab manager ne certificate approve kiya
 * TRIGGER: When manager approves certificate
 * RECIPIENT: Intern
 * ============================================
 */

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class CertificateApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;
    
    protected $certificateType;
    protected $downloadUrl;
    protected $internName;
    
    public function __construct(string $certificateType, string $downloadUrl, string $internName)
    {
        $this->certificateType = $certificateType;
        $this->downloadUrl = $downloadUrl;
        $this->internName = $internName;
    }
    
    public function via($notifiable)
    {
        return ['mail'];
    }
    
    public function toMail($notifiable)
    {
        $typeLabel = str_replace('_', ' ', ucfirst($this->certificateType));
        
        return (new MailMessage)
            ->subject("🎓 Your Certificate is Ready!")
            ->greeting("Congratulations {$this->internName}!")
            ->line("Your **{$typeLabel}** has been approved and generated.")
            ->line("You can now download your certificate from the portal.")
            ->action('Download Certificate', $this->downloadUrl)
            ->line('Thank you for completing your internship with Ezitech!');
    }
}