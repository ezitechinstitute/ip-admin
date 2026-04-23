<?php

/**
 * ============================================
 * NOTIFICATION SYSTEM UPDATE - PHASE 4
 * ============================================
 * Date: 2026-04-18
 * 
 * CHANGES MADE:
 * - Created brand new UnifiedNotificationService
 * 
 * REASON:
 * - Sab notifications ko ek jagah se bhejne ke liye central service chahiye
 * - Pehle alag alag jagah notification logic tha, maintain karna mushkil tha
 * - Email + Portal bell (notification) dono ek saath handle karega
 * - Har notification type ke liye alag code nahi likhna parega
 * 
 * WHAT THIS SERVICE DOES:
 * 1. Portal notification (bell icon) - user role ke hisaab se table mein save karega
 *    - Intern → intern_notifications table
 *    - Supervisor → supervisor_notifications table
 *    - Admin/Manager → portal_notifications table
 * 
 * 2. Email notification - queue mein dalega aur background mein bhejega
 *    - Tumhara existing template use karega: mail.notification
 *    - Variables: $name, $messageBody match karte hain tumhare template se
 *    - Email log bhi maintain karega (email_logs table)
 * 
 * AFFECTED MODULES:
 * - All notification sending in the system
 * - Will be used by PortalFreezeService, Controllers, etc.
 * 
 * USAGE EXAMPLE:
 * $notificationService = app(UnifiedNotificationService::class);
 * $notificationService->send(
 *     $intern,           // recipient object
 *     'intern',          // role
 *     'task_approved',   // type
 *     'Task Approved',   // title
 *     'Your task has been approved', // message
 *     ['action_url' => '/tasks/123'] // optional data
 * );
 * ============================================
 */

namespace App\Services;

use App\Models\PortalNotification;
use App\Models\EmailLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class UnifiedNotificationService
{
    /**
     * Send notification to any user
     * 
     * @param mixed $recipient User object (InternAccount, ManagersAccount, AdminAccount, etc.)
     * @param string $role User role: 'admin', 'manager', 'supervisor', 'intern'
     * @param string $type Notification type (task_submitted, invoice_reminder, etc.)
     * @param string $title Email/Notification title
     * @param string $message Notification message content
     * @param array $data Extra data (action_url, reference_id, etc.)
     * @return bool Success/failure
     */
    public function send($recipient, string $role, string $type, string $title, string $message, array $data = [])
    {
        try {
            // Get user details from recipient object or ID
            $userId = $this->getUserId($recipient, $role);
            $email = $this->getUserEmail($recipient, $role);
            $name = $this->getUserName($recipient, $role);
            
            if (!$userId) {
                Log::warning("Cannot send notification: No user ID for role {$role}");
                return false;
            }
            
            // 1. Store portal notification (Bell icon)
            $notificationId = $this->storePortalNotification($userId, $role, $type, $title, $message, $data);
            
            // 2. Send email (if email exists and not disabled)
            if ($email && ($data['send_email'] ?? true)) {
                $this->sendEmail($email, $name, $title, $message, $data, $notificationId);
            }
            
            Log::info("Notification sent", [
                'user_id' => $userId,
                'role' => $role,
                'type' => $type,
                'title' => $title
            ]);
            
            return true;
            
        } catch (\Exception $e) {
            Log::error("UnifiedNotificationService::send failed: " . $e->getMessage(), [
                'role' => $role,
                'type' => $type,
                'recipient' => is_object($recipient) ? get_class($recipient) : 'scalar'
            ]);
            return false;
        }
    }
    
    /**
     * Store notification in correct table based on user role
     */
    private function storePortalNotification($userId, string $role, string $type, string $title, string $message, array $data)
    {
        // INTERNS -> intern_notifications table (already exists in DB)
        if ($role === 'intern') {
            $id = DB::table('intern_notifications')->insertGetId([
                'intern_id' => $userId,
                'title' => $title,
                'message' => $message,
                'type' => $type,
                'is_read' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            return $id;
        }
        
        // SUPERVISORS -> supervisor_notifications table (already exists in DB)
        if ($role === 'supervisor') {
            $id = DB::table('supervisor_notifications')->insertGetId([
                'supervisor_id' => $userId,
                'title' => $title,
                'message' => $message,
                'is_read' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            return $id;
        }
        
        // ADMIN / MANAGER -> portal_notifications table (updated in Phase 2)
        $notification = PortalNotification::create([
            'user_id' => $userId,
            'role' => $role,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'reference_id' => $data['reference_id'] ?? null,
            'action_url' => $data['action_url'] ?? null,
            'is_read' => false,
            'is_email_sent' => false,
        ]);
        
        return $notification->id;
    }
    
    /**
     * Send email using YOUR existing template
     * Template location: resources/views/mail/notification.blade.php
     */
    private function sendEmail(string $email, string $name, string $title, string $message, array $data, $notificationId = null)
    {
        try {
            // Using YOUR existing template with CORRECT variable names
            // Template expects: {{ $name }} and {{ $messageBody }}
            Mail::queue('mail.notification', [
                'name' => $name,              // matches {{ $name }} in template
                'messageBody' => $message,    // matches {{ $messageBody }} in template
            ], function($mail) use ($email, $name, $title) {
                $mail->to($email, $name)
                    ->subject($title)
                    ->from(config('mail.from.address'), 'Ezitech Intern Portal');
            });
            
            // Log email attempt for tracking
            EmailLog::create([
                'notification_id' => $notificationId,
                'recipient_email' => $email,
                'recipient_name' => $name,
                'recipient_role' => $data['role'] ?? null,
                'subject' => $title,
                'status' => 'queued',
            ]);
            
            // Update portal notification if exists
            if ($notificationId && $notificationId !== 'intern' && $notificationId !== 'supervisor') {
                PortalNotification::where('id', $notificationId)->update([
                    'is_email_sent' => true,
                    'sent_at' => now()
                ]);
            }
            
        } catch (\Exception $e) {
            Log::error("Email send failed: " . $e->getMessage());
            
            // Log failure
            EmailLog::create([
                'notification_id' => $notificationId,
                'recipient_email' => $email,
                'recipient_name' => $name,
                'subject' => $title,
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
        }
    }
    
    /**
     * Get user ID from recipient object
     */
    private function getUserId($recipient, string $role)
    {
        if (is_object($recipient)) {
            return match($role) {
                'intern' => $recipient->int_id ?? $recipient->id ?? null,
                'manager' => $recipient->manager_id ?? $recipient->id ?? null,
                'supervisor' => $recipient->supervisor_id ?? $recipient->id ?? null,
                'admin' => $recipient->id ?? null,
                default => $recipient->id ?? null,
            };
        }
        return $recipient;
    }
    
    /**
     * Get user email from recipient object
     */
    private function getUserEmail($recipient, string $role)
    {
        if (is_object($recipient)) {
            return $recipient->email ?? null;
        }
        return null;
    }
    
    /**
     * Get user name from recipient object
     */
    private function getUserName($recipient, string $role)
    {
        if (is_object($recipient)) {
            return $recipient->name ?? ucfirst($role);
        }
        return ucfirst($role);
    }
    
    /**
     * Send bulk notification to multiple users
     * 
     * @param array $recipients Array of recipient objects
     * @param string $role Role of all recipients
     * @param string $type Notification type
     * @param string $title Notification title
     * @param string $message Notification message
     * @param array $data Extra data
     */
    public function sendBulk(array $recipients, string $role, string $type, string $title, string $message, array $data = [])
    {
        $successCount = 0;
        $failCount = 0;
        
        foreach ($recipients as $recipient) {
            if ($this->send($recipient, $role, $type, $title, $message, $data)) {
                $successCount++;
            } else {
                $failCount++;
            }
        }
        
        Log::info("Bulk notification sent", [
            'type' => $type,
            'role' => $role,
            'success' => $successCount,
            'failed' => $failCount
        ]);
        
        return ['success' => $successCount, 'failed' => $failCount];
    }
    
    /**
     * Get unread notification count for a user (for bell icon badge)
     */
    public function getUnreadCount($userId, string $role): int
    {
        try {
            if ($role === 'intern') {
                return DB::table('intern_notifications')
                    ->where('intern_id', $userId)
                    ->where('is_read', false)
                    ->count();
            }
            
            if ($role === 'supervisor') {
                return DB::table('supervisor_notifications')
                    ->where('supervisor_id', $userId)
                    ->where('is_read', false)
                    ->count();
            }
            
            return PortalNotification::where('user_id', $userId)
                ->where('role', $role)
                ->where('is_read', false)
                ->count();
                
        } catch (\Exception $e) {
            Log::error("Failed to get unread count: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Mark notification as read
     */
    public function markAsRead($notificationId, $userId, string $role): bool
    {
        try {
            if ($role === 'intern') {
                DB::table('intern_notifications')
                    ->where('id', $notificationId)
                    ->where('intern_id', $userId)
                    ->update(['is_read' => true]);
                return true;
            }
            
            if ($role === 'supervisor') {
                DB::table('supervisor_notifications')
                    ->where('id', $notificationId)
                    ->where('supervisor_id', $userId)
                    ->update(['is_read' => true]);
                return true;
            }
            
            return PortalNotification::where('id', $notificationId)
                ->where('user_id', $userId)
                ->where('role', $role)
                ->update(['is_read' => true]);
                
        } catch (\Exception $e) {
            Log::error("Failed to mark notification as read: " . $e->getMessage());
            return false;
        }
    }
}