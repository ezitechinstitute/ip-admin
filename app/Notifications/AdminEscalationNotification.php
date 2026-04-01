<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class AdminEscalationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $escalationType;
    protected $internName;
    protected $internId;
    protected $managerName;
    protected $managerId;
    protected $hoursElapsed;

    public function __construct($escalationType, $internName, $internId, $managerName = 'Unknown', $managerId = null, $hoursElapsed = 8)
    {
        $this->escalationType = $escalationType;
        $this->internName = $internName;
        $this->internId = $internId;
        $this->managerName = $managerName;
        $this->managerId = $managerId;
        $this->hoursElapsed = $hoursElapsed;
    }

    public function via($notifiable)
    {
        return ['mail', 'database']; // email + dashboard notification
    }

    public function toMail($notifiable)
    {
        $typeLabel = ucfirst($this->escalationType);
        return (new MailMessage)
                    ->subject("🚨 ESCALATION ALERT: Unresolved $typeLabel - Manager {$this->managerId}")
                    ->greeting('Admin Team,')
                    ->line("**ESCALATION ALERT** - A {$this->escalationType} for intern **{$this->internName}** has been unresolved for **{$this->hoursElapsed}+ hours**.")
                    ->line("**Manager:** {$this->managerName} (ID: {$this->managerId})")
                    ->line('**Status:** Manager reminder was sent but issue remains unresolved.')
                    ->line('**Action Required:** Please contact the manager and ensure this issue is resolved immediately.')
                    ->action('View Escalation Details', url("/admin/escalations/{$this->internId}"))
                    ->line('This is an automated escalation alert.');
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'admin_escalation',
            'escalation_type' => $this->escalationType,
            'intern_name' => $this->internName,
            'intern_id' => $this->internId,
            'manager_name' => $this->managerName,
            'manager_id' => $this->managerId,
            'hours_elapsed' => $this->hoursElapsed,
            'message' => "⚠️ Escalation: {$this->escalationType} for {$this->internName} unresolved for {$this->hoursElapsed}+ hours (Manager: {$this->managerName})",
            'action_url' => "/admin/escalations/{$this->internId}",
            'severity' => 'high',
        ];
    }
}
