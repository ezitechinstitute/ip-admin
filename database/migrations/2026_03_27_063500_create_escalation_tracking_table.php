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
        if (!Schema::hasTable('escalation_tracking')) {
            Schema::create('escalation_tracking', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('intern_id')->nullable();
                $table->unsignedInteger('manager_id')->nullable();
                $table->enum('escalation_type', ['interview', 'test'])->comment('Type of escalation: interview or test');
                $table->enum('escalation_level', ['manager_reminder', 'admin_alert'])->default('manager_reminder')->comment('Escalation level: 1=Manager Reminder, 2=Admin Alert');
                $table->timestamp('escalated_at')->nullable()->comment('When escalation was triggered');
                $table->timestamp('resolved_at')->nullable()->comment('When issue was resolved');
                $table->text('notes')->nullable()->comment('Escalation notes');
                $table->boolean('notified_admin')->default(false)->comment('Whether admin has been notified');
                $table->timestamps();
                
                // Don't use foreign keys initially to avoid constraint errors
                // Will be added in migration if table structures match
                $table->index('manager_id');
                $table->index('intern_id');
                $table->index('escalation_type');
                $table->index('escalation_level');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('escalation_tracking');
    }
};
