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

protected $casts = [
    'due_date' => 'date',
];
}

