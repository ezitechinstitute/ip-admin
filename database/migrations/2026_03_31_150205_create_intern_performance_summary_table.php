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
        if (!Schema::hasTable('intern_performance_summary')) {
            Schema::create('intern_performance_summary', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('intern_id');
                $table->enum('period', ['weekly', 'monthly', 'quarterly', 'yearly'])->default('monthly');
                $table->integer('period_number'); // week number (1-52) or month number (1-12)
                $table->integer('year');
                
                // Aggregated metrics
                $table->integer('tasks_completed')->default(0);
                $table->integer('tasks_total')->default(0);
                $table->decimal('task_completion_rate', 5, 2)->default(0);
                
                $table->integer('projects_completed')->default(0);
                $table->integer('projects_total')->default(0);
                
                $table->decimal('average_task_score', 5, 2)->default(0);
                $table->decimal('attendance_percentage', 5, 2)->default(0);
                
                $table->integer('total_points')->default(0);
                $table->string('grade')->nullable();
                
                $table->timestamps();

                $table->index('intern_id');
                $table->unique(['intern_id', 'period', 'period_number', 'year'], 'unique_period_performance');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('intern_performance_summary');
    }
};