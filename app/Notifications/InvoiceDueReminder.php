<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Invoice;

class InvoiceDueReminder extends Notification implements ShouldQueue
{
    use Queueable;

    protected $invoice;

    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

    public function via($notifiable)
    {
        //return ['mail', 'database']; 
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Invoice Due Reminder')
                    ->line('Reminder: Invoice is due in 4 days.')
                    ->line('Intern: ' . $this->invoice->name)
                    ->line('Invoice Type: ' . ucfirst($this->invoice->invoice_type))
                    ->line('Remaining Amount: ' . $this->invoice->remaining_amount)
                    ->line('Due Date: ' . $this->invoice->due_date->format('d M Y'))
                    ->action('View Invoice', url('/manager/invoices')); // update URL
    }

    /*

    public function toDatabase($notifiable)
    {
        return [
            'invoice_id' => $this->invoice->id,
            'intern_name' => $this->invoice->name,
            'invoice_type' => $this->invoice->invoice_type,
            'remaining_amount' => $this->invoice->remaining_amount,
            'due_date' => $this->invoice->due_date,
            'message' => 'Invoice due in 4 days'
        ];
    }     
  */
}