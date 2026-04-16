<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add timestamp fields to intern_table if they don't exist
        if (Schema::hasTable('intern_table') && !Schema::hasColumn('intern_table', 'interview_completed_at')) {
            Schema::table('intern_table', function (Blueprint $table) {
                $table->timestamp('interview_completed_at')->nullable()->after('status')->comment('When interview was completed');
                $table->timestamp('test_completed_at')->nullable()->after('interview_completed_at')->comment('When test was completed');
                $table->string('test_status')->default('pending')->after('test_completed_at')->comment('Status of test: pending, in-progress, completed, passed, failed');
            });
        }

        // Add shift_hours to manager_accounts if it doesn't exist
        if (Schema::hasTable('manager_accounts') && !Schema::hasColumn('manager_accounts', 'shift_hours')) {
            Schema::table('manager_accounts', function (Blueprint $table) {
                $table->integer('shift_hours')->default(8)->after('comission')->comment('Manager working hours per day');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop added columns
        if (Schema::hasTable('intern_table')) {
            Schema::table('intern_table', function (Blueprint $table) {
                if (Schema::hasColumn('intern_table', 'interview_completed_at')) {
                    $table->dropColumn('interview_completed_at');
                }
                if (Schema::hasColumn('intern_table', 'test_completed_at')) {
                    $table->dropColumn('test_completed_at');
                }
                if (Schema::hasColumn('intern_table', 'test_status')) {
                    $table->dropColumn('test_status');
                }
            });
        }

        if (Schema::hasTable('manager_accounts')) {
            Schema::table('manager_accounts', function (Blueprint $table) {
                if (Schema::hasColumn('manager_accounts', 'shift_hours')) {
                    $table->dropColumn('shift_hours');
                }
            });
        }
    }
};
