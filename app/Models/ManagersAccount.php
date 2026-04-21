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
 * - Managers ko notifications receive hoti hain (new interns, tasks, etc.)
 * - Without this method, manager email notifications will fail
 * 
 * AFFECTED MODULES:
 * - All email notifications sent to Managers
 * - NewInternAssignedNotification
 * - TaskSubmittedNotification
 * - InternshipCompletionNotification
 * - CertificateRequestedNotification
 * - Invoice reminders
 * 
 * BEFORE: No routeNotificationForMail() method
 * AFTER: routeNotificationForMail() returns email
 * ============================================
 */

namespace App\Models;

// English comments: Crucial change! Extend Authenticatable instead of base Model
use Illuminate\Foundation\Auth\User as Authenticatable; 
use Illuminate\Notifications\Notifiable;

class ManagersAccount extends Authenticatable
{
    use Notifiable;

    protected $table = 'manager_accounts'; 

    protected $primaryKey = 'manager_id';

    public $incrementing = true; 

    protected $fillable = [
        'eti_id',
        'image',
        'name',
        'email',
        'contact',
        'join_date',
        'password',
        'comission',
        'department',
        'status',
        'loginas',
        'emergency_contact',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Automatically trim whitespace from password
     */
    public function getPasswordAttribute($value)
    {
        return trim($value);
    }

    
    // [NEW] ADDED: Route notifications to email
   
     
    public function routeNotificationForMail()
    {
        return $this->email;
    }
}