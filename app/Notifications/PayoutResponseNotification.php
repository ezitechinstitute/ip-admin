<?php

/**
 * ============================================
 * NOTIFICATION SYSTEM UPDATE - PHASE 5
 * ============================================
 * Date: 2026-04-18
 * 
 * PURPOSE: Jab admin ne payout approve/reject kiya
 * TRIGGER: When admin approves/rejects payout
 * RECIPIENT: Manager
 * ============================================
 */

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class PayoutResponseNotification extends Notification implements ShouldQueue
{
    use Queueable;
    
    protected $request;
    protected $status;
    protected $adminNote;
    
    public function __construct($request, string $status, string $adminNote = '')
    {
        $this->request = $request;
        $this->status = $status;
        $this->adminNote = $adminNote;
    }
    
    public function via($notifiable)
    {
        return ['mail'];
    }
    
    public function toMail($notifiable)
    {
        $emoji = $this->status === 'approved' ? '✅' : '❌';
        $statusText = ucfirst($this->status);
        
        $mail = (new MailMessage)
            ->subject("{$emoji} Payout Request {$statusText}")
            ->greeting("Hi {$notifiable->name},")
            ->line("Your payout request has been **{$statusText}**.")
            ->line("**Requested Amount:** PKR " . number_format($this->request->amount, 0));
        
        if ($this->status === 'approved') {
            $mail->line("The amount will be credited to your registered account within 3-5 business days.");
        }
        
        if ($this->adminNote) {
            $mail->line("**Note from Admin:** {$this->adminNote}");
        }
        
        return $mail->action('View Details', url("/manager/payouts/{$this->request->id}"));
    }
}