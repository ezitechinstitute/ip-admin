<?php

/**
 * ============================================
 * NOTIFICATION SYSTEM UPDATE - PHASE 2
 * ============================================
 * Date: 2026-04-18
 * 
 * CHANGES MADE:
 * - Added routeNotificationForMail() method
 * 
 * REASON:
 * - Laravel ko batana zaroori hai ke email kis column mein store hai
 * - Interns ko notifications receive hoti hain (task reviews, certificates, etc.)
 * - Without this method, intern email notifications will fail
 * 
 * AFFECTED MODULES:
 * - All email notifications sent to Interns
 * - TaskReviewedNotification (when task approved/rejected)
 * - CertificateApprovedNotification
 * - PortalFrozenNotification
 * 
 * NOTE: isFrozen(), freeze(), unfreeze() methods already EXIST
 * Unhe change karne ki zaroorat nahi hai
 * 
 * BEFORE: No routeNotificationForMail() method
 * AFTER: routeNotificationForMail() returns email
 * ============================================
 */

namespace App\Models;

// English comments: Crucial change! Extend Authenticatable instead of base Model
use Illuminate\Foundation\Auth\User as Authenticatable; 
use Illuminate\Notifications\Notifiable;

class InternAccount extends Authenticatable
{
    use Notifiable;

    protected $table = 'intern_accounts';

    protected $primaryKey = 'int_id';

    public $incrementing = true;

    protected $fillable = [
        'int_id',
        'eti_id',
        'name',
        'email',
        'phone',
        'password',
        'int_technology',
        'start_date',
        'int_status',
        'portal_status',
        'review',
        'reset_token',
        'supervisor_id',
        'manager_id',
        'profile_photo',
        'city',
        'university',
        'bio',
        'skills',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public $timestamps = false;

    /**
     * Automatically trim whitespace from password
     */
    public function getPasswordAttribute($value)
    {
        return trim($value);
    }

    /**
     * Store password as plain text (no hashing)
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = $value;
    }

    // Relationships
    public function supervisor()
    {
        return $this->belongsTo(ManagersAccount::class, 'supervisor_id', 'manager_id');
    }

    public function manager()
    {
        return $this->belongsTo(ManagersAccount::class, 'manager_id', 'manager_id');
    }

    // Scopes
    public function scopeFrozen($query)
    {
        return $query->where('portal_status', 'frozen');
    }

    public function scopeActive($query)
    {
        return $query->where('portal_status', 'active');
    }

    public function scopePendingActivation($query)
    {
        return $query->where('portal_status', 'pending_activation');
    }

    // Helper methods
    public function isFrozen(): bool
    {
        return $this->portal_status === 'frozen';
    }

    public function isActive(): bool
    {
        return $this->portal_status === 'active';
    }

    public function isPendingActivation(): bool
    {
        return $this->portal_status === 'pending_activation';
    }

    public function freeze()
    {
        return $this->update(['portal_status' => 'frozen']);
    }

    public function unfreeze()
    {
        return $this->update(['portal_status' => 'active']);
    }

    public function activate()
    {
        return $this->update(['portal_status' => 'active']);
    }

    // ========== new METHOD ( - Only adding) ==========

/**
 * Get invoices for this intern - NEW relationship
 */
public function invoices()
{
    return $this->hasMany(\App\Models\invoice::class, 'intern_email', 'email');
}

    /**
     * [NEW] ADDED: Route notifications to email
     */
    public function routeNotificationForMail()
    {
        return $this->email;
    }
}