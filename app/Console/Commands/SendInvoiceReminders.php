<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Invoice;
use App\Models\User; // Manager
use App\Notifications\InvoiceDueReminder;
use Carbon\Carbon;

class SendInvoiceReminders extends Command
{
    protected $signature = 'invoice:send-reminders';
    protected $description = 'Send reminders for invoices due in 4 days';

    public function handle()
    {
        $targetDate = Carbon::now()->addDays(4)->toDateString();

        $invoices = Invoice::where('remaining_amount', '>', 0)
                    ->whereDate('due_date', $targetDate)
                    ->get();

        foreach ($invoices as $invoice) {
            // Manager ko notification send karein
            if ($invoice->manager_id) {
                $manager = User::find($invoice->manager_id);
                $manager->notify(new InvoiceDueReminder($invoice));
            }

            // Optional: Intern ko bhi email
            // $intern = User::where('email', $invoice->intern_email)->first();
            // if ($intern) {
            //     $intern->notify(new InvoiceDueReminder($invoice));
            // }
        }

        $this->info('Invoice reminders sent successfully!');
    }
}