<?php

/**
 * ============================================
 * NOTIFICATION SYSTEM UPDATE - PHASE 5
 * ============================================
 * Date: 2026-04-18
 * 
 * PURPOSE: Jab manager ne payout request kiya
 * TRIGGER: When manager requests payout
 * RECIPIENT: Admin
 * ============================================
 */

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class PayoutRequestedNotification extends Notification implements ShouldQueue
{
    use Queueable;
    
    protected $request;
    protected $requesterName;
    protected $requesterRole;
    
    public function __construct($request, string $requesterName, string $requesterRole = 'manager')
    {
        $this->request = $request;
        $this->requesterName = $requesterName;
        $this->requesterRole = $requesterRole;
    }
    
    public function via($notifiable)
    {
        return ['mail'];
    }
    
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject("💸 Payout Request: {$this->requesterName}")
            ->greeting('Admin Team,')
            ->line("A payout request has been submitted and requires your approval.")
            ->line("**Requester:** {$this->requesterName} ({$this->requesterRole})")
            ->line("**Amount:** PKR " . number_format($this->request->amount, 0))
            ->line("**Bank:** {$this->request->bank}")
            ->line("**Account Number:** {$this->request->ac_no}")
            ->action('Review Request', url("/admin/payouts/{$this->request->id}"))
            ->line('Please review and approve/reject the request.');
    }
}