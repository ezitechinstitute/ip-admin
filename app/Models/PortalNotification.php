<?php

/**
 * ============================================
 * NOTIFICATION SYSTEM UPDATE - PHASE 2
 * ============================================
 * Date: 2026-04-18
 * 
 * CHANGES MADE:
 * - Added 'role', 'type', 'reference_id', 'action_url', 'is_email_sent', 'sent_at' to fillable
 * 
 * REASON:
 * - Database mein naye columns add ho chuke hain (Phase 1)
 * - Model ko batana zaroori hai ke ye fields fillable hain
 * - Otherwise mass assignment error aayega
 * 
 * AFFECTED MODULES:
 * - UnifiedNotificationService (portal notification store karta hai)
 * - Admin/Manager notification bell
 * 
 * BEFORE: 4 fillable fields
 * AFTER:  10 fillable fields
 * ============================================
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PortalNotification extends Model
{
    protected $fillable = [
        'user_id',
        'role',           // [NEW] ADDED - Role filter ke liye (admin/manager/supervisor/intern)
        'type',           // [NEW] ADDED - Notification type identify karne ke liye
        'title',
        'message',
        'reference_id',   // [NEW] ADDED - Related record ka ID (task_id, invoice_id, etc.)
        'action_url',     // [NEW] ADDED - "View Details" button ka link
        'is_read',
        'is_email_sent',  // [NEW] ADDED - Email already send hui ya nahi track karne ke liye
        'sent_at'         // [NEW] ADDED - Notification kab send hui timestamp
    ];
}