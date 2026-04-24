<?php

/**
 * ============================================
 * NOTIFICATION SYSTEM UPDATE - MIGRATION 1
 * ============================================
 * Date: 2026-04-18
 * 
 * CHANGES MADE:
 * - Add 'role' column to identify user type (admin/manager/supervisor/intern)
 * - Add 'type' column for notification category
 * - Add 'reference_id' column to link to related records
 * - Add 'action_url' column for button links
 * - Add 'is_email_sent' column to track email delivery
 * - Add 'sent_at' column for timestamp tracking
 * 
 * REASON:
 * - Existing portal_notifications table only had basic fields
 * - Need these columns for proper notification filtering and tracking
 * - Role column needed to filter notifications per panel (admin/manager/intern)
 * - Type column needed to identify what kind of notification (task/invoice/etc.)
 * 
 * AFFECTED MODULES:
 * - Admin Panel (notification bell)
 * - Manager Panel (notification bell)
 * - Intern Panel (uses separate intern_notifications table, not affected)
 * 
 * ROLLBACK:
 * - Run php artisan migrate:rollback (will remove all added columns)
 * 
 * BEFORE: id, user_id, title, message, is_read, created_at, updated_at
 * AFTER:  id, user_id, role, type, title, message, reference_id, 
 *         action_url, is_read, is_email_sent, created_at, updated_at, sent_at
 * ============================================
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToPortalNotificationsTable extends Migration
{
    public function up()
    {
        Schema::table('portal_notifications', function (Blueprint $table) {
            
            // [NEW] ADDED: Role column - user type ke basis par filter karne ke liye
            // Values: 'admin', 'manager', 'supervisor', 'intern'
            if (!Schema::hasColumn('portal_notifications', 'role')) {
                $table->enum('role', ['admin', 'manager', 'supervisor', 'intern'])
                      ->default('manager')
                      ->after('user_id');
            }
            
            // [NEW] ADDED: Type column - notification category identify karne ke liye
            // Examples: 'task_submitted', 'invoice_reminder', 'certificate_approved', etc.
            if (!Schema::hasColumn('portal_notifications', 'type')) {
                $table->string('type', 50)->nullable()->after('role');
            }
            
            // [NEW] ADDED: Reference ID - related record ka ID store karne ke liye
            // Examples: task_id, invoice_id, intern_id, etc.
            if (!Schema::hasColumn('portal_notifications', 'reference_id')) {
                $table->integer('reference_id')->nullable()->after('message');
            }
            
            // [NEW] ADDED: Action URL - "View Details" button ka link
            if (!Schema::hasColumn('portal_notifications', 'action_url')) {
                $table->string('action_url')->nullable()->after('reference_id');
            }
            
            // [NEW] ADDED: Is Email Sent - email already bheji ya nahi track karne ke liye
            if (!Schema::hasColumn('portal_notifications', 'is_email_sent')) {
                $table->boolean('is_email_sent')->default(false)->after('is_read');
            }
            
            // [NEW] ADDED: Sent At - notification kab send hui timestamp
            if (!Schema::hasColumn('portal_notifications', 'sent_at')) {
                $table->timestamp('sent_at')->nullable()->after('created_at');
            }
        });
    }

    public function down()
    {
        // WARNING: Production mein down migration nahi chalana
        // Agar wapas jaana ho to manually columns hatao:
        // ALTER TABLE portal_notifications DROP COLUMN role, type, reference_id, action_url, is_email_sent, sent_at;
        
        Schema::table('portal_notifications', function (Blueprint $table) {
            $table->dropColumn(['role', 'type', 'reference_id', 'action_url', 'is_email_sent', 'sent_at']);
        });
    }
}