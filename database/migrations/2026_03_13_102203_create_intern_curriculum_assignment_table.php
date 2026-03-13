<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('intern_curriculum_assignment', function (Blueprint $table) {
            $table->id('assignment_id');

            // Just store eti_id without foreign key
            $table->string('eti_id', 255)->charset('utf8mb4')->collation('utf8mb4_unicode_ci');

            $table->unsignedBigInteger('curriculum_id');
            $table->foreign('curriculum_id')
                  ->references('curriculum_id')
                  ->on('technology_curriculum')
                  ->onDelete('restrict');

            // Match manager_accounts.manager_id type
            $table->integer('assigned_by');
            $table->foreign('assigned_by')
                  ->references('manager_id')
                  ->on('manager_accounts');

            $table->integer('current_project_index')->default(1);
            $table->dateTime('assigned_date')->useCurrent();
            $table->date('start_date')->nullable();
            $table->date('expected_end_date')->nullable();
            $table->date('actual_end_date')->nullable();
            $table->enum('status',['active','completed','paused','cancelled'])->default('active');
            $table->integer('completion_percentage')->default(0);
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('intern_curriculum_assignment');
    }
};