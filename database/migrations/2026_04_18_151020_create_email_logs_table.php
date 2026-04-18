<?php

/**
 * ============================================
 * NOTIFICATION SYSTEM UPDATE - MIGRATION 3
 * ============================================
 * Date: 2026-04-18
 * 
 * CHANGES MADE:
 * - Create brand new 'email_logs' table for email tracking
 * 
 * REASON:
 * - Production mein email send hoti hai ya nahi track karna important hai
 * - Agar email fail hoti hai to pata ho ke kyun fail hui
 * - Debugging ke liye helpful hai
 * - Support tickets ke liye proof hota hai ke email bheji thi ya nahi
 * 
 * AFFECTED MODULES:
 * - Notification System (all email sending)
 * - Admin Panel (can view email logs in future)
 * 
 * TABLE STRUCTURE:
 * - id: Primary key
 * - notification_id: Link to portal_notifications (nullable)
 * - recipient_email: Kisko email bheji
 * - recipient_name: Receiver ka name
 * - recipient_role: admin/manager/supervisor/intern
 * - subject: Email subject
 * - status: pending/sent/failed/queued
 * - error_message: Agar fail hui to kyun
 * - sent_at: Kab send hui
 * - timestamps: created_at, updated_at
 * 
 * NOTE: This is a NEW table, not modifying existing ones
 * ============================================
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmailLogsTable extends Migration
{
    public function up()
    {
        // Check if table already exists (safety check)
        if (!Schema::hasTable('email_logs')) {
            Schema::create('email_logs', function (Blueprint $table) {
                $table->id();
                
                // [NEW] Link to portal_notifications (if notification was stored)
                $table->unsignedBigInteger('notification_id')->nullable();
                
                // [NEW] Recipient information
                $table->string('recipient_email');
                $table->string('recipient_name')->nullable();
                $table->string('recipient_role', 50)->nullable();
                
                // [NEW] Email content tracking
                $table->string('subject', 500);
                
                // [NEW] Status tracking - email send hui ya nahi
                $table->enum('status', ['pending', 'sent', 'failed', 'queued'])->default('pending');
                
                // [NEW] Error details - agar fail hui to
                $table->text('error_message')->nullable();
                
                // [NEW] When email was actually sent
                $table->timestamp('sent_at')->nullable();
                
                // [NEW] Laravel timestamps
                $table->timestamps();
                
                // [NEW] Indexes for faster queries
                $table->index('status');
                $table->index('created_at');
                $table->index('recipient_email');
            });
        }
    }

    public function down()
    {
        // WARNING: Production mein down migration nahi chalana (data loss)
        Schema::dropIfExists('email_logs');
    }
}