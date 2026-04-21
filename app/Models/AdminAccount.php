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
 * - Jab Notification::send() call hota hai to email address yahan se le ga
 * - Without this method, email notifications will fail silently
 * 
 * AFFECTED MODULES:
 * - All email notifications sent to Admin
 * - AdminEscalationNotification
 * - PayoutRequestedNotification
 * 
 * BEFORE: No routeNotificationForMail() method
 * AFTER: routeNotificationForMail() returns email
 * ============================================
 */

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable; 
use Illuminate\Notifications\Notifiable;

class AdminAccount extends Authenticatable
{
    use Notifiable;

    protected $table = 'admin_accounts';

    protected $fillable = [
        'name', 
        'email', 
        'password', 
        'loginas', 
        'image'
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
     * [NEW] ADDED: Route notifications to email
     * Reason: Laravel needs to know which column contains the email address
     * When notification is sent, this method is called automatically
     */
    public function routeNotificationForMail()
    {
        return $this->email;
    }
}