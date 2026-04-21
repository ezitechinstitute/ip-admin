<?php


/**
 * ============================================
 * NOTIFICATION SYSTEM UPDATE - PHASE 2
 * ============================================
 * Date: 2026-04-18
 * 
 * CHANGES MADE:
 * - Added 'reminder_sent_at' and 'freeze_notified_at' to fillable array
 * 
 * REASON:
 * - Database mein naye columns add ho chuke hain (Phase 1)
 * - SendInvoiceReminders command reminder_sent_at update karta hai
 * - PortalFreezeService freeze_notified_at update karta hai
 * - Without these in fillable, mass assignment error aayega
 * 
 * AFFECTED MODULES:
 * - SendInvoiceReminders command (invoice:send-reminders)
 * - PortalFreezeService (freeze enforcement)
 * 
 * BEFORE: 11 fillable fields
 * AFTER:  13 fillable fields
 * ============================================
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class invoice extends Model
{
    protected $table= 'invoices';

    //   public $timestamps = false;
protected $fillable = [
    'inv_id',
    'screenshot',
    'name',
    'contact',
    'intern_email',
    'total_amount',
    'received_amount',
    'remaining_amount',
    'due_date', 
    'received_by',
    'status',
    'invoice_type', 
    'created_at',
    'reminder_sent_at',    // [NEW] ADDED - Duplicate reminders rokne ke liye
    'freeze_notified_at',  // [NEW] ADDED - Duplicate freeze notifications rokne ke liye
];


// ========== ADD THESE new METHODS ( - Only adding, not removing) ==========

/**
 * Get payment status - NEW accessor
 * Old code still works with $invoice->remaining_amount <= 0 logic
 */
public function getPaymentStatusAttribute()
{
    if ($this->remaining_amount <= 0) return 'Paid';
    if ($this->due_date && $this->due_date < now()) return 'Overdue';
    return 'Pending';
}

/**
 * Get status badge class - NEW accessor
 */
public function getStatusBadgeClassAttribute()
{
    return match($this->payment_status) {
        'Paid' => 'success',
        'Overdue' => 'danger',
        default => 'warning',
    };
}

/**
 * Get formatted phone - NEW accessor
 */
public function getFormattedPhoneAttribute()
{
    $phone = $this->contact;
    if (strlen($phone) > 10) {
        return substr($phone, 0, 4) . ' ' . substr($phone, 4, 3) . ' ' . substr($phone, 7);
    }
    return $phone;
}

/**
 * Get intern initials - NEW accessor
 */
public function getInitialsAttribute()
{
    $nameParts = explode(' ', trim($this->name));
    return strtoupper(substr($nameParts[0], 0, 1) . (isset($nameParts[1]) ? substr($nameParts[1], 0, 1) : ''));
}

/**
 * Get formatted total amount - NEW accessor
 */
public function getFormattedTotalAttribute()
{
    return 'PKR ' . number_format($this->total_amount);
}

/**
 * Get formatted received amount - NEW accessor
 */
public function getFormattedReceivedAttribute()
{
    return 'PKR ' . number_format($this->received_amount);
}

/**
 * Get formatted remaining amount - NEW accessor
 */
public function getFormattedRemainingAttribute()
{
    return 'PKR ' . number_format($this->remaining_amount);
}

/**
 * Get formatted due date - NEW accessor
 */
public function getFormattedDueDateAttribute()
{
    return $this->due_date ? $this->due_date->format('d M, Y') : 'N/A';
}

// ========== NEW SCOPES for cleaner queries ==========

public function scopePaid($query)
{
    return $query->where('remaining_amount', '<=', 0);
}

public function scopePending($query)
{
    return $query->where('remaining_amount', '>', 0)
                 ->where(function($q) {
                     $q->whereNull('due_date')->orWhere('due_date', '>=', now());
                 });
}

public function scopeOverdue($query)
{
    return $query->where('remaining_amount', '>', 0)
                 ->where('due_date', '<', now());
}

protected $casts = [
    'due_date' => 'date',
];
}

