<?php

/**
 * ============================================
 * NOTIFICATION SYSTEM UPDATE - MIGRATION 2
 * ============================================
 * Date: 2026-04-18
 * 
 * CHANGES MADE:
 * - Add 'reminder_sent_at' column to prevent duplicate reminder emails
 * - Add 'freeze_notified_at' column to prevent duplicate freeze notifications
 * 
 * REASON:
 * - SendInvoiceReminders command was sending duplicate emails every day
 * - Without tracking, same invoice reminder sends 30+ times (once per cron run)
 * - Need to mark when reminder was sent to avoid duplicates
 * - Portal freeze notification should only send once, not every day
 * 
 * AFFECTED MODULES:
 * - Manager Panel (invoice reminders)
 * - Cron Jobs (SendInvoiceReminders command)
 * - Portal Freeze System (freeze notifications)
 * 
 * BEFORE: invoices table had no tracking columns
 * AFTER:  invoices table has reminder_sent_at and freeze_notified_at
 * 
 * FIXES ISSUE: Duplicate invoice reminders 
 * ============================================
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReminderTrackingToInvoicesTable extends Migration
{
    public function up()
    {
        Schema::table('invoices', function (Blueprint $table) {
            
            // [NEW] ADDED: Reminder Sent At - invoice reminder kab bheji track karne ke liye
            // Purpose: Sirf ek baar reminder bhejni hai, har roz nahi
            if (!Schema::hasColumn('invoices', 'reminder_sent_at')) {
                $table->timestamp('reminder_sent_at')->nullable()->after('due_date');
            }
            
            // [NEW] ADDED: Freeze Notified At - portal freeze notification kab bheji track karne ke liye
            // Purpose: Freeze notification sirf ek baar bhejni hai, har roz nahi
            if (!Schema::hasColumn('invoices', 'freeze_notified_at')) {
                $table->timestamp('freeze_notified_at')->nullable()->after('reminder_sent_at');
            }
        });
    }

    public function down()
    {
        // WARNING: Production mein down migration nahi chalana
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['reminder_sent_at', 'freeze_notified_at']);
        });
    }
}