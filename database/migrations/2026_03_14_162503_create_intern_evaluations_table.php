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
        if (!Schema::hasTable('intern_evaluations')) {
            Schema::create('intern_evaluations', function (Blueprint $table) {
                $table->id();
                $table->string('eti_id'); // From intern_accounts
                $table->integer('supervisor_id'); // From manager_accounts
                $table->string('month');
                $table->integer('technical_skills')->default(0);
                $table->integer('problem_solving')->default(0);
                $table->integer('communication')->default(0);
                $table->integer('task_completion')->default(0);
                $table->decimal('overall_score', 5, 2)->default(0.00);
                $table->text('remarks')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('intern_evaluations');
    }
};
