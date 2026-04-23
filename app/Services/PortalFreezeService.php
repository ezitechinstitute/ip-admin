<?php

/**
 * ============================================
 * NOTIFICATION SYSTEM UPDATE - PHASE 4
 * ============================================
 * Date: 2026-04-18
 * 
 * CHANGES MADE:
 * - Added UnifiedNotificationService dependency injection
 * - Added notification sending when portal is frozen
 * - Added freeze_notified_at tracking to prevent duplicate notifications
 * 
 * REASON:
 * - Jab intern ka portal freeze ho, usko email aur portal notification milni chahiye
 * - Pehle freeze hota tha lekin intern ko pata nahi chalta tha kyun
 * - freeze_notified_at column se ensure karte hain ke sirf ek baar notification jaye
 * - Har roz notification nahi bhejni chahiye (annoying hota hai)
 * 
 * AFFECTED MODULES:
 * - Portal freeze system
 * - Intern notifications
 * 
 * FIXES ISSUE: Portal freeze without notification 
 * 
 * BEFORE: Portal freeze hota tha lekin intern ko kuch nahi pata chalta tha
 * AFTER: Intern ko email + portal notification milti hai
 * ============================================
 */

namespace App\Services;

use App\Models\InternAccount;
use App\Models\Invoice;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\UnifiedNotificationService; //new

class PortalFreezeService
{



     /**
     * @var UnifiedNotificationService
     */
    protected $notificationService;
    
    /**
     * Constructor with dependency injection
     * 
     * [NEW] ADDED: UnifiedNotificationService injected automatically by Laravel container
     * Reason: Portal freeze hone par intern ko notify karne ke liye
     */
    public function __construct(UnifiedNotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }
    
    /**
     * Check for overdue invoices and freeze intern portals
     * Also sends notification to intern when frozen
     */




    /**
     * Check and freeze interns with overdue invoices
     * This should be run by a scheduled command or job
     */
    public function enforcePaymentFreeze()
    {
        try {
            $now = now();

            // Find invoices that are overdue and have remaining balance
            $overdueInvoices = Invoice::where('due_date', '<', $now)
                ->where('remaining_amount', '>', 0)
                ->whereNull('freeze_notified_at')  // [NEW] Sirf ek baar notify karo
                ->get();

            $frozenCount = 0;
            $notifiedCount = 0; 

            foreach ($overdueInvoices as $invoice) {
                try {
                    // Find intern by various methods (inv_id, intern_email, or name)
                    $intern = $this->findInternForInvoice($invoice);

                    if ($intern && !$intern->isFrozen()) {
                        // Freeze the portal
                        $intern->freeze();
                        $frozenCount++;

                                                
                        // [NEW] Send notification to intern
                        $this->notificationService->send(
                            $intern,
                            'intern',
                            'portal_frozen',
                            '⚠️ Your Portal Has Been Frozen',
                            "Your portal has been frozen due to overdue payment of PKR " . 
                            number_format($invoice->remaining_amount, 0) . 
                            ". Please contact your manager immediately to restore access.",
                            [
                                'action_url' => '/intern/invoices',
                                'reference_id' => $invoice->id,
                                'send_email' => true
                            ]
                        );
                        $notifiedCount++;
                        // Mark as notified so we don't send again tomorrow
                        $invoice->update(['freeze_notified_at' => now()]);

                        // Log the action
                        Log::info("Portal frozen for intern: {$intern->name} (ID: {$intern->int_id})", [
                            'invoice_id' => $invoice->id,
                            'due_date' => $invoice->due_date,
                            'remaining_amount' => $invoice->remaining_amount,
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::error("Error freezing portal for invoice {$invoice->id}: {$e->getMessage()}");
                    continue;
                }
            }

            return [
                'success' => true,
                'message' => "Froze {$frozenCount} intern portals with overdue invoices",
                'frozen_count' => $frozenCount,
                'notified_count' => $notifiedCount,
            ];
        } catch (\Exception $e) {
            Log::error("Portal freeze enforcement failed: {$e->getMessage()}");
            return [
                'success' => false,
                'message' => "Error during portal freeze enforcement: {$e->getMessage()}",
                'frozen_count' => 0,
            ];
        }
    }

    /**
     * Unfreeze intern portal when payment is received
     */
    public function unfreezeOnPayment($internId)
    {
        try {
            $intern = InternAccount::find($internId);

            if (!$intern) {
                return false;
            }

            // Check if all invoices for this intern are paid
            $unpaidInvoices = Invoice::where(function ($query) use ($intern) {
                $query->where('intern_email', $intern->email)
                    ->orWhere('name', $intern->name);
            })
            ->where('remaining_amount', '>', 0)
            ->where('due_date', '<', now())
            ->count();

            // If no unpaid overdue invoices, unfreeze
            if ($unpaidInvoices === 0) {
                $intern->unfreeze();

                // [NEW] Send unfreeze notification
                $this->notificationService->send(
                    $intern,
                    'intern',
                    'portal_unfrozen',
                    '✅ Your Portal Has Been Unfrozen',
                    "Your portal has been unfrozen. You can now access all features again.",
                    ['action_url' => '/intern/dashboard']
                );
                
              




                Log::info("Portal unfrozen for intern: {$intern->name} (ID: {$intern->int_id})", [
                    'reason' => 'Payment cleared',
                ]);
                return true;
            }

            return false;
        } catch (\Exception $e) {
            Log::error("Error unfreezing portal: {$e->getMessage()}");
            return false;
        }
    }

    /**
     * Helper: Find intern account associated with an invoice
     */
    private function findInternForInvoice(Invoice $invoice)
    {
        // Try to find by intern_email
        if ($invoice->intern_email) {
            $intern = InternAccount::where('email', $invoice->intern_email)->first();
            if ($intern) return $intern;
        }

        // Try to find by name
        if ($invoice->name) {
            $intern = InternAccount::where('name', $invoice->name)->first();
            if ($intern) return $intern;
        }

        // Try to find by eti_id if invoice has it
        if (isset($invoice->eti_id) && $invoice->eti_id) {
            $intern = InternAccount::where('eti_id', $invoice->eti_id)->first();
            if ($intern) return $intern;
        }

        return null;
    }

    /**
     * Get portal freeze status for an intern
     */
    public function getInternStatus($internId)
    {
        $intern = InternAccount::find($internId);

        if (!$intern) {
            return null;
        }

        // Find overdue unpaid invoices
        $overdueInvoices = Invoice::where(function ($query) use ($intern) {
            $query->where('intern_email', $intern->email)
                ->orWhere('name', $intern->name);
        })
        ->where('due_date', '<', now())
        ->where('remaining_amount', '>', 0)
        ->get();

        return [
            'intern_id' => $intern->int_id,
            'name' => $intern->name,
            'portal_status' => $intern->portal_status,
            'is_frozen' => $intern->isFrozen(),
            'overdue_invoices' => $overdueInvoices->count(),
            'total_overdue_amount' => $overdueInvoices->sum('remaining_amount'),
            'overdue_details' => $overdueInvoices->map(function ($inv) {
                return [
                    'invoice_id' => $inv->id,
                    'due_date' => $inv->due_date,
                    'remaining_amount' => $inv->remaining_amount,
                    'days_overdue' => now()->diffInDays($inv->due_date),
                ];
            }),
        ];
    }
}
