<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PortalFreezeHelper
{
    /**
     * Check if intern portal is frozen due to overdue payments
     * Automatically reactivates when payment is cleared
     * 
     * @param string $internEmail
     * @return bool
     */
    public static function isFrozen($internEmail)
    {
        try {
            // Safety check: if no email provided, return false
            if (empty($internEmail)) {
                return false;
            }
            
            // Check if invoices table exists (production safety)
            if (!DB::getSchemaBuilder()->hasTable('invoices')) {
                return false;
            }
            
            // Check for ANY unpaid overdue invoices
            // If no overdue invoices exist, portal is automatically ACTIVE
            $hasOverdue = DB::table('invoices')
                ->where('intern_email', $internEmail)
                ->where('remaining_amount', '>', 0)
                ->where('due_date', '<', Carbon::now())
                ->exists();
            
            // If no overdue invoices → Portal is ACTIVE ✅
            // If overdue invoices exist → Portal is FROZEN ❌
            return $hasOverdue;
            
        } catch (\Exception $e) {
            // Log error but don't break the application
            Log::error('PortalFreezeHelper::isFrozen error: ' . $e->getMessage());
            return false; // Default to NOT frozen on error (production safety)
        }
    }
    
    /**
     * Get freeze status with message for intern
     * 
     * @param string $internEmail
     * @return array ['frozen' => bool, 'message' => string|null]
     */
    public static function getStatus($internEmail)
    {
        try {
            if (self::isFrozen($internEmail)) {
                return [
                    'frozen' => true,
                    'message' => '❌ Your portal is frozen due to overdue payment. Please clear your dues to continue.'
                ];
            }
            
            return [
                'frozen' => false,
                'message' => null
            ];
            
        } catch (\Exception $e) {
            Log::error('PortalFreezeHelper::getStatus error: ' . $e->getMessage());
            return [
                'frozen' => false,
                'message' => null
            ];
        }
    }
    
    /**
     * Check if intern has any pending payments (not necessarily overdue)
     * 
     * @param string $internEmail
     * @return bool
     */
    public static function hasPendingPayments($internEmail)
    {
        try {
            if (empty($internEmail)) {
                return false;
            }
            
            $hasPending = DB::table('invoices')
                ->where('intern_email', $internEmail)
                ->where('remaining_amount', '>', 0)
                ->exists();
            
            return $hasPending;
            
        } catch (\Exception $e) {
            Log::error('PortalFreezeHelper::hasPendingPayments error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get total pending amount for intern
     * 
     * @param string $internEmail
     * @return float
     */
    public static function getPendingAmount($internEmail)
    {
        try {
            if (empty($internEmail)) {
                return 0;
            }
            
            $totalPending = DB::table('invoices')
                ->where('intern_email', $internEmail)
                ->where('remaining_amount', '>', 0)
                ->sum('remaining_amount');
            
            return floatval($totalPending);
            
        } catch (\Exception $e) {
            Log::error('PortalFreezeHelper::getPendingAmount error: ' . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Get overdue amount for intern
     * 
     * @param string $internEmail
     * @return float
     */
    public static function getOverdueAmount($internEmail)
    {
        try {
            if (empty($internEmail)) {
                return 0;
            }
            
            $overdueAmount = DB::table('invoices')
                ->where('intern_email', $internEmail)
                ->where('remaining_amount', '>', 0)
                ->where('due_date', '<', Carbon::now())
                ->sum('remaining_amount');
            
            return floatval($overdueAmount);
            
        } catch (\Exception $e) {
            Log::error('PortalFreezeHelper::getOverdueAmount error: ' . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Get freeze summary for dashboard display
     * 
     * @param string $internEmail
     * @return array
     */
    public static function getFreezeSummary($internEmail)
    {
        try {
            $isFrozen = self::isFrozen($internEmail);
            $pendingAmount = self::getPendingAmount($internEmail);
            $overdueAmount = self::getOverdueAmount($internEmail);
            
            if ($isFrozen) {
                return [
                    'is_frozen' => true,
                    'status' => 'Frozen',
                    'message' => '⚠️ Your portal is frozen due to overdue payment of PKR ' . number_format($overdueAmount, 2),
                    'pending_amount' => $pendingAmount,
                    'overdue_amount' => $overdueAmount,
                ];
            }
            
            if ($pendingAmount > 0) {
                return [
                    'is_frozen' => false,
                    'status' => 'Active',
                    'message' => 'You have pending payment of PKR ' . number_format($pendingAmount, 2) . '. Please clear before due date.',
                    'pending_amount' => $pendingAmount,
                    'overdue_amount' => 0,
                ];
            }
            
            return [
                'is_frozen' => false,
                'status' => 'Active',
                'message' => null,
                'pending_amount' => 0,
                'overdue_amount' => 0,
            ];
            
        } catch (\Exception $e) {
            Log::error('PortalFreezeHelper::getFreezeSummary error: ' . $e->getMessage());
            return [
                'is_frozen' => false,
                'status' => 'Active',
                'message' => null,
                'pending_amount' => 0,
                'overdue_amount' => 0,
            ];
        }
    }
    
    /**
     * Reactivate portal by clearing all overdue flags (when payment is made)
     * This is automatically handled by isFrozen() check, but this method can be used explicitly
     * 
     * @param string $internEmail
     * @return bool
     */
    public static function reactivatePortal($internEmail)
    {
        try {
            // No explicit action needed - isFrozen() will return false when no overdue invoices
            // This method exists for clarity and any additional reactivation logic
            
            Log::info('Portal reactivation check triggered for: ' . $internEmail);
            
            // Optional: Send notification to intern
            $intern = DB::table('intern_accounts')->where('email', $internEmail)->first();
            if ($intern && DB::getSchemaBuilder()->hasTable('intern_notifications')) {
                DB::table('intern_notifications')->insert([
                    'intern_id' => $intern->int_id,
                    'title' => 'Portal Reactivated',
                    'message' => 'Your portal has been reactivated. You can now submit tasks and access all features.',
                    'type' => 'portal',
                    'is_read' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            
            return true;
            
        } catch (\Exception $e) {
            Log::error('PortalFreezeHelper::reactivatePortal error: ' . $e->getMessage());
            return false;
        }
    }
}