<?php

/**
 * ============================================
 * NOTIFICATION SYSTEM UPDATE - PHASE 2
 * ============================================
 * Date: 2026-04-18
 * 
 * CHANGES MADE:
 * - Created brand new EmailLog model
 * 
 * REASON:
 * - Email logs track karne ke liye model chahiye
 * - Har email send hone par record store hoga
 * - Failed emails debug karne mein help karega
 * - Support tickets mein proof ke taur par use hoga
 * 
 * AFFECTED MODULES:
 * - UnifiedNotificationService (email logs store karta hai)
 * - Admin Panel (future mein email logs dekh sakte hain)
 * 
 * TABLE: email_logs (created in Phase 1)
 * ============================================
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailLog extends Model
{
    protected $table = 'email_logs';
    
    protected $fillable = [
        'notification_id',
        'recipient_email',
        'recipient_name',
        'recipient_role',
        'subject',
        'status',
        'error_message',
        'sent_at'
    ];
    
    protected $casts = [
        'sent_at' => 'datetime',
    ];
    
    /**
     * Get human-readable status label
     */
    public function getStatusLabelAttribute()
    {
        $labels = [
            'pending' => '⏳ Pending',
            'queued' => '📤 Queued',
            'sent' => '✅ Sent',
            'failed' => '❌ Failed',
        ];
        
        return $labels[$this->status] ?? $this->status;
    }
    
    /**
     * Check if email was sent successfully
     */
    public function isSent(): bool
    {
        return $this->status === 'sent';
    }
    
    /**
     * Check if email failed
     */
    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }
    
    /**
     * Scope for failed emails
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }
    
    /**
     * Scope for pending emails
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}