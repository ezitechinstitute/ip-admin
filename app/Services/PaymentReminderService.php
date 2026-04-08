<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\InternAccount;
use App\Models\ManagersAccount;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PaymentReminderService
{
    /**
     * Get invoices that need reminders (due in specified days)
     */
    public function getInvoicesDueIn($days = 4)
    {
        $targetDate = Carbon::now()->addDays($days)->toDateString();

        return Invoice::where('remaining_amount', '>', 0)
                    ->whereDate('due_date', $targetDate)
                    ->get();
    }

    /**
     * Get overdue invoices (past due date)
     */
    public function getOverdueInvoices()
    {
        return Invoice::where('remaining_amount', '>', 0)
                    ->where('due_date', '<', now()->toDateString())
                    ->get();
    }

    /**
     * Get invoices approaching due date (5-7 days remaining)
     */
    public function getUpcomingInvoices($daysRange = [5, 7])
    {
        $startDate = Carbon::now()->addDays($daysRange[0])->toDateString();
        $endDate = Carbon::now()->addDays($daysRange[1])->toDateString();

        return Invoice::where('remaining_amount', '>', 0)
                    ->whereBetween('due_date', [$startDate, $endDate])
                    ->get();
    }

    /**
     * Send manual reminder for specific invoice
     */
    public function sendInvoiceReminder($invoiceId)
    {
        $invoice = Invoice::find($invoiceId);

        if (!$invoice) {
            Log::warning("Invoice not found: {$invoiceId}");
            return false;
        }

        try {
            // Send to manager
            if ($invoice->manager_id) {
                $manager = ManagersAccount::where('manager_id', $invoice->manager_id)->first();
                if ($manager && $manager->email) {
                    $this->sendManagerReminderEmail($invoice, $manager);
                }
            }

            // Send to intern
            $intern = $this->findInternForInvoice($invoice);
            if ($intern && $intern->email) {
                $this->sendInternReminderEmail($invoice, $intern);
            }

            return true;
        } catch (\Exception $e) {
            Log::error("Error sending reminder for invoice {$invoiceId}: {$e->getMessage()}");
            return false;
        }
    }

    /**
     * Get payment status summary for managers
     */
    public function getPaymentSummary($managerId)
    {
        $invoices = Invoice::where('manager_id', $managerId)->get();

        $summary = [
            'total_invoices' => $invoices->count(),
            'total_amount' => $invoices->sum('total_amount'),
            'received_amount' => $invoices->sum('received_amount'),
            'remaining_amount' => $invoices->sum('remaining_amount'),
            'paid_invoices' => $invoices->where('remaining_amount', '<=', 0)->count(),
            'pending_invoices' => $invoices->where('remaining_amount', '>', 0)->count(),
            'overdue_invoices' => $invoices->filter(function ($inv) {
                return $inv->remaining_amount > 0 && $inv->due_date < now();
            })->count(),
            'upcoming_invoices' => $invoices->filter(function ($inv) {
                return $inv->remaining_amount > 0 && 
                       $inv->due_date >= now() && 
                       $inv->due_date <= now()->addDays(7);
            })->count(),
            'total_overdue_amount' => $invoices->filter(function ($inv) {
                return $inv->remaining_amount > 0 && $inv->due_date < now();
            })->sum('remaining_amount'),
        ];

        return $summary;
    }

    /**
     * Helper: Find intern for invoice
     */
    private function findInternForInvoice($invoice)
    {
        if ($invoice->intern_email) {
            return InternAccount::where('email', $invoice->intern_email)->first();
        }

        if ($invoice->name) {
            return InternAccount::where('name', $invoice->name)->first();
        }

        return null;
    }

    /**
     * Send formatted email to manager
     */
    private function sendManagerReminderEmail($invoice, $manager)
    {
        $subject = "💰 Invoice Due Reminder: PKR " . number_format($invoice->remaining_amount, 0) . 
                  " Due on " . Carbon::parse($invoice->due_date)->format('d M Y');

        $html = $this->getManagerReminderhtml($invoice, $manager);

        Mail::html($html, function ($mail) use ($manager, $subject) {
            $mail->to($manager->email)
                ->subject($subject)
                ->from(config('mail.from.address'), config('mail.from.name'));
        });
    }

    /**
     * Send formatted email to intern
     */
    private function sendInternReminderEmail($invoice, $intern)
    {
        $subject = "💰 Invoice Due Reminder: PKR " . number_format($invoice->remaining_amount, 0) . 
                  " Due on " . Carbon::parse($invoice->due_date)->format('d M Y');

        $html = $this->getInternReminderHtml($invoice, $intern);

        Mail::html($html, function ($mail) use ($intern, $subject) {
            $mail->to($intern->email)
                ->subject($subject)
                ->from(config('mail.from.address'), config('mail.from.name'));
        });
    }

    /**
     * HTML template for manager reminder
     */
    private function getManagerReminderHtml($invoice, $manager)
    {
        $daysUntilDue = Carbon::parse($invoice->due_date)->diffInDays(now());
        
        return "
<div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
    <div style='background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 5px 5px 0 0;'>
        <h2 style='margin: 0;'>💰 Payment Reminder</h2>
    </div>
    <div style='background: #f9f9f9; padding: 20px; border-radius: 0 0 5px 5px;'>
        <p>Dear <strong>{$manager->name}</strong>,</p>
        
        <p>This is a reminder that an invoice payment is due soon.</p>
        
        <table style='width: 100%; border-collapse: collapse; margin: 20px 0; background: white;'>
            <tr style='background: #f0f0f0;'>
                <td style='padding: 10px; border: 1px solid #ddd; font-weight: bold;'>Invoice Details</td>
                <td style='padding: 10px; border: 1px solid #ddd;'></td>
            </tr>
            <tr>
                <td style='padding: 10px; border: 1px solid #ddd;'>Intern Name:</td>
                <td style='padding: 10px; border: 1px solid #ddd;'>{$invoice->name}</td>
            </tr>
            <tr style='background: #fafafa;'>
                <td style='padding: 10px; border: 1px solid #ddd;'>Invoice ID:</td>
                <td style='padding: 10px; border: 1px solid #ddd;'>#{$invoice->id}</td>
            </tr>
            <tr>
                <td style='padding: 10px; border: 1px solid #ddd;'>Total Amount:</td>
                <td style='padding: 10px; border: 1px solid #ddd;'><strong>PKR " . number_format($invoice->total_amount, 0) . "</strong></td>
            </tr>
            <tr style='background: #fafafa;'>
                <td style='padding: 10px; border: 1px solid #ddd;'>Paid Amount:</td>
                <td style='padding: 10px; border: 1px solid #ddd;'>PKR " . number_format($invoice->received_amount, 0) . "</td>
            </tr>
            <tr style='background: #fff3cd;'>
                <td style='padding: 10px; border: 1px solid #ddd; font-weight: bold;'>Remaining Amount:</td>
                <td style='padding: 10px; border: 1px solid #ddd; font-weight: bold; color: #e74c3c;'>PKR " . number_format($invoice->remaining_amount, 0) . "</td>
            </tr>
            <tr style='background: #fff3cd;'>
                <td style='padding: 10px; border: 1px solid #ddd; font-weight: bold;'>Due Date:</td>
                <td style='padding: 10px; border: 1px solid #ddd; font-weight: bold;'>" . Carbon::parse($invoice->due_date)->format('d M Y') . " ({$daysUntilDue} days remaining)</td>
            </tr>
        </table>

        <div style='background: #fff3cd; border-left: 4px solid #e74c3c; padding: 15px; margin: 20px 0; border-radius: 3px;'>
            <strong style='color: #e74c3c;'>⚠️ Important Notice:</strong>
            <p style='margin: 10px 0 0 0;'>If payment is not completed by the due date, the intern's portal will be automatically frozen, preventing them from submitting tasks and accessing course materials.</p>
        </div>

        <p style='font-size: 12px; color: #666; margin-top: 20px;'>
            Best regards,<br>
            <strong>Ezitech Internship Platform</strong>
        </p>
    </div>
</div>
        ";
    }

    /**
     * HTML template for intern reminder
     */
    private function getInternReminderHtml($invoice, $intern)
    {
        $daysUntilDue = Carbon::parse($invoice->due_date)->diffInDays(now());
        
        return "
<div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
    <div style='background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 5px 5px 0 0;'>
        <h2 style='margin: 0;'>💰 Invoice Payment Reminder</h2>
    </div>
    <div style='background: #f9f9f9; padding: 20px; border-radius: 0 0 5px 5px;'>
        <p>Dear <strong>{$intern->name}</strong>,</p>
        
        <p>This is a friendly reminder that your internship invoice payment is due soon.</p>
        
        <table style='width: 100%; border-collapse: collapse; margin: 20px 0; background: white;'>
            <tr style='background: #f0f0f0;'>
                <td style='padding: 10px; border: 1px solid #ddd; font-weight: bold;'>Invoice Details</td>
                <td style='padding: 10px; border: 1px solid #ddd;'></td>
            </tr>
            <tr>
                <td style='padding: 10px; border: 1px solid #ddd;'>Invoice ID:</td>
                <td style='padding: 10px; border: 1px solid #ddd;'>#{$invoice->id}</td>
            </tr>
            <tr style='background: #fafafa;'>
                <td style='padding: 10px; border: 1px solid #ddd;'>Total Amount:</td>
                <td style='padding: 10px; border: 1px solid #ddd;'><strong>PKR " . number_format($invoice->total_amount, 0) . "</strong></td>
            </tr>
            <tr>
                <td style='padding: 10px; border: 1px solid #ddd;'>Paid Amount:</td>
                <td style='padding: 10px; border: 1px solid #ddd;'>PKR " . number_format($invoice->received_amount, 0) . "</td>
            </tr>
            <tr style='background: #fafafa;'>
                <td style='padding: 10px; border: 1px solid #ddd; font-weight: bold;'>Remaining Amount:</td>
                <td style='padding: 10px; border: 1px solid #ddd; font-weight: bold; color: #e74c3c;'>PKR " . number_format($invoice->remaining_amount, 0) . "</td>
            </tr>
            <tr style='background: #fff3cd;'>
                <td style='padding: 10px; border: 1px solid #ddd; font-weight: bold;'>Due Date:</td>
                <td style='padding: 10px; border: 1px solid #ddd; font-weight: bold;'>" . Carbon::parse($invoice->due_date)->format('d M Y') . " ({$daysUntilDue} days remaining)</td>
            </tr>
        </table>

        <div style='background: #fff3cd; border-left: 4px solid #e74c3c; padding: 15px; margin: 20px 0; border-radius: 3px;'>
            <strong style='color: #e74c3c;'>⚠️ Important Notice:</strong>
            <p style='margin: 10px 0 0 0;'>If this payment is not made by the due date, your internship portal will be <strong>automatically frozen</strong>. This will prevent you from:</p>
            <ul style='margin: 10px 0; padding-left: 20px;'>
                <li>Submitting tasks and projects</li>
                <li>Accessing course materials</li>
                <li>Requesting certificates</li>
            </ul>
            <p style='margin: 10px 0 0 0;'>Please contact your manager immediately to arrange payment.</p>
        </div>

        <p style='font-size: 12px; color: #666; margin-top: 20px;'>
            Best regards,<br>
            <strong>Ezitech Internship Platform</strong>
        </p>
    </div>
</div>
        ";
    }
}
