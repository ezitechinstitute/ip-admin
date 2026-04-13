<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Invoice;
use App\Models\InternAccount;
use App\Models\ManagersAccount;
use App\Notifications\InvoiceDueReminder;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendInvoiceReminders extends Command
{
    protected $signature = 'invoice:send-reminders {--days=4 : Days before due date to send reminder}';
    protected $description = 'Send reminders for invoices due in specified days (default 4 days)';

    public function handle()
    {
        $daysBeforeDue = $this->option('days');
        $targetDate = Carbon::now()->addDays($daysBeforeDue)->toDateString();

        $this->info("🔔 Sending invoice reminders for invoices due on {$targetDate}...");

        try {
            $invoices = Invoice::where('remaining_amount', '>', 0)
                        ->whereDate('due_date', $targetDate)
                        ->get();

            $remindersSent = 0;

            foreach ($invoices as $invoice) {
                try {
                    // Send reminder to Manager
                    if ($invoice->manager_id) {
                        $manager = ManagersAccount::where('manager_id', $invoice->manager_id)->first();
                        if ($manager && $manager->email) {
                            $this->sendManagerReminder($invoice, $manager);
                            $remindersSent++;
                            Log::info("Invoice reminder sent to manager: {$manager->email}", ['invoice_id' => $invoice->id]);
                        }
                    }

                    // Send reminder to Intern
                    if ($invoice->intern_email) {
                        $intern = InternAccount::where('email', $invoice->intern_email)->first();
                        if ($intern && $intern->email) {
                            $this->sendInternReminder($invoice, $intern);
                            $remindersSent++;
                            Log::info("Invoice reminder sent to intern: {$intern->email}", ['invoice_id' => $invoice->id]);
                        }
                    } elseif ($invoice->name) {
                        // Try to find by name
                        $intern = InternAccount::where('name', $invoice->name)->first();
                        if ($intern && $intern->email) {
                            $this->sendInternReminder($invoice, $intern);
                            $remindersSent++;
                            Log::info("Invoice reminder sent to intern: {$intern->email}", ['invoice_id' => $invoice->id]);
                        }
                    }

                } catch (\Exception $e) {
                    Log::error("Error sending reminder for invoice {$invoice->id}: {$e->getMessage()}");
                    $this->error("Failed to send reminder for invoice {$invoice->id}: {$e->getMessage()}");
                    continue;
                }
            }

            $this->info("✅ Sent {$remindersSent} invoice reminders!");
            return Command::SUCCESS;

        } catch (\Exception $e) {
            Log::error("Invoice reminders command failed: {$e->getMessage()}");
            $this->error("❌ Error: {$e->getMessage()}");
            return Command::FAILURE;
        }
    }

    /**
     * Send reminder email to manager
     */
    private function sendManagerReminder($invoice, $manager)
    {
        try {
            $subject = "⏰ Invoice Payment Reminder: Due on " . Carbon::parse($invoice->due_date)->format('d M Y');
            
            $message = "
Hello {$manager->name},

This is a payment reminder for invoice #{$invoice->id}.

<strong>Invoice Details:</strong>
- Intern Name: {$invoice->name}
- Invoice Amount: PKR " . number_format($invoice->total_amount, 2) . "
- Paid Amount: PKR " . number_format($invoice->received_amount, 2) . "
- Remaining Amount: PKR " . number_format($invoice->remaining_amount, 2) . "
- Due Date: " . Carbon::parse($invoice->due_date)->format('d M Y') . "
- Days Until Due: " . Carbon::parse($invoice->due_date)->diffInDays(now()) . " days

Please arrange for payment before the due date to avoid portal freeze.

If payment has already been made, please disregard this message.

Best regards,
Ezitech Internship Platform
            ";

            // Try using notification if available, otherwise send direct email
            if (method_exists($manager, 'notify')) {
                $manager->notify(new InvoiceDueReminder($invoice));
            } else {
                // Fallback: Send direct email
                Mail::raw($message, function ($mail) use ($manager, $subject) {
                    $mail->to($manager->email)
                        ->subject($subject);
                });
            }
        } catch (\Exception $e) {
            Log::error("Error sending manager reminder: {$e->getMessage()}");
            throw $e;
        }
    }

    /**
     * Send reminder email to intern
     */
    private function sendInternReminder($invoice, $intern)
    {
        try {
            $subject = "⏰ Invoice Payment Reminder: Due on " . Carbon::parse($invoice->due_date)->format('d M Y');
            
            $message = "
Hello {$intern->name},

This is a payment reminder for your internship invoice.

<strong>Invoice Details:</strong>
- Invoice Amount: PKR " . number_format($invoice->total_amount, 2) . "
- Paid Amount: PKR " . number_format($invoice->received_amount, 2) . "
- Remaining Amount: PKR " . number_format($invoice->remaining_amount, 2) . "
- Due Date: " . Carbon::parse($invoice->due_date)->format('d M Y') . "
- Days Until Due: " . Carbon::parse($invoice->due_date)->diffInDays(now()) . " days

<strong>Important:</strong> If payment is not made by the due date, your internship portal will be frozen and you won't be able to submit tasks or access course materials.

Please contact your manager for payment arrangement.

Best regards,
Ezitech Internship Platform
            ";

            // Send via notification or direct email
            if (method_exists($intern, 'notify')) {
                $intern->notify(new InvoiceDueReminder($invoice));
            } else {
                // Fallback: Send direct email
                Mail::raw($message, function ($mail) use ($intern, $subject) {
                    $mail->to($intern->email)
                        ->subject($subject);
                });
            }
        } catch (\Exception $e) {
            Log::error("Error sending intern reminder: {$e->getMessage()}");
            throw $e;
        }
    }
}
