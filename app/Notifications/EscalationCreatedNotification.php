<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class EscalationCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $escalationType;
    protected $internName;
    protected $internId;
    protected $lastUpdateTime;
    protected $escalationId;

    public function __construct($escalationType, $internName, $internId, $escalationId, $lastUpdateTime = null)
    {
        $this->escalationType = $escalationType;
        $this->internName = $internName;
        $this->internId = $internId;
        $this->escalationId = $escalationId;
        $this->lastUpdateTime = $lastUpdateTime;
    }

    public function via($notifiable)
    {
        return ['mail', 'database']; // email + dashboard notification
    }

    public function toMail($notifiable)
    {
        $typeLabel = ucfirst($this->escalationType);
        $actionUrl = url("/manager/escalations/{$this->escalationId}");
        
        return (new MailMessage)
                    ->subject("⚠️ ESCALATION NOTICE: {$typeLabel} Status - {$this->internName}")
                    ->greeting("Dear Manager,")
                    ->line("An escalation has been created for your oversight.")
                    ->line("**Intern:** {$this->internName}")
                    ->line("**Escalation Type:** {$typeLabel}")
                    ->line("**Status:** This {$typeLabel} has not been updated for 8 hours and requires your immediate attention.")
                    ->line("**Required Action:** Please review the status and take appropriate action to resolve this issue.")
                    ->action('View Escalation Details', $actionUrl)
                    ->line('If you have already resolved this issue, please disregard this notice.')
                    ->line('This is an automated escalation reminder.');
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'escalation_created',
            'escalation_type' => $this->escalationType,
            'intern_name' => $this->internName,
            'intern_id' => $this->internId,
            'escalation_id' => $this->escalationId,
            'message' => "⚠️ Escalation Created: {$this->escalationType} for {$this->internName} - requires your attention",
            'action_url' => "/manager/escalations/{$this->escalationId}",
            'severity' => 'medium',
        ];
    }
}
