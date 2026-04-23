<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ManagerReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $escalationType;
    protected $internName;
    protected $internId;
    protected $hoursElapsed;
    protected $managerName;

    public function __construct($escalationType, $internName, $internId, $hoursElapsed = 8, $managerName = 'Manager')
    {
        $this->escalationType = $escalationType;
        $this->internName = $internName;
        $this->internId = $internId;
        $this->hoursElapsed = $hoursElapsed;
        $this->managerName = $managerName;
    }

    public function via($notifiable)
    {
        // return ['mail', 'database']; 

        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $typeLabel = ucfirst($this->escalationType);
        return (new MailMessage)
                    ->subject("⏰ Reminder: Pending $typeLabel - Action Required")
                    ->greeting("Hi {$this->managerName},")
                    ->line("A {$this->escalationType} for intern **{$this->internName}** has been pending for **{$this->hoursElapsed} hours**.")
                    ->line('Please take immediate action to prevent further escalation.')
                    ->line("If this is an error or the issue is already resolved, please update the status.")
                    ->action('View Intern Details', url("/manager/interns/{$this->internId}"))
                    ->line('Thank you for your prompt attention to this matter.');
    }

   /* public function toDatabase($notifiable)
    {
        return [
            'type' => 'manager_reminder',
            'escalation_type' => $this->escalationType,
            'intern_name' => $this->internName,
            'intern_id' => $this->internId,
            'hours_elapsed' => $this->hoursElapsed,
            'message' => "Pending {$this->escalationType} for {$this->internName} (last {$this->hoursElapsed} hours)",
            'action_url' => "/manager/interns/{$this->internId}",
        ];
    }   
        */
}
