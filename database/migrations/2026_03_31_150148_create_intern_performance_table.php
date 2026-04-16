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
        if (!Schema::hasTable('intern_performance')) {
            Schema::create('intern_performance', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('intern_id');
                $table->integer('week_number')->nullable();
                $table->integer('month_number')->nullable();
                $table->integer('year')->nullable();
                
                // Performance metrics
                $table->integer('tasks_completed')->default(0);
                $table->integer('tasks_total')->default(0);
                $table->decimal('task_completion_rate', 5, 2)->default(0);
                
                $table->integer('projects_completed')->default(0);
                $table->integer('projects_total')->default(0);
                $table->decimal('project_completion_rate', 5, 2)->default(0);
                
                $table->decimal('average_task_score', 5, 2)->default(0);
                $table->decimal('attendance_percentage', 5, 2)->default(0);
                
                $table->integer('points_earned')->default(0);
                $table->integer('points_total')->default(0);
                
                $table->string('grade')->nullable(); // A, B, C, D, F
                $table->text('supervisor_feedback')->nullable();
                
                $table->timestamps();

                // Indexes
                $table->index('intern_id');
                $table->index(['intern_id', 'week_number']);
                $table->index(['intern_id', 'month_number', 'year']);
                $table->unique(['intern_id', 'week_number', 'year'], 'unique_week_performance');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('intern_performance');
    }
};