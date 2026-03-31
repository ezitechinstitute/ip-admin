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
       if (!Schema::hasTable('intern_project_progress')) {
           Schema::create('intern_project_progress', function (Blueprint $table) {
        $table->id('progress_id');
        $table->unsignedBigInteger('assignment_id');
        $table->unsignedBigInteger('cp_id');
        $table->date('start_date')->nullable();
        $table->date('end_date')->nullable();
        $table->enum('status',['pending','in_progress','completed','overdue'])->default('pending');
        $table->integer('progress_percentage')->default(0);
        $table->unsignedBigInteger('supervisor_id')->nullable();
        $table->text('supervisor_remarks')->nullable();
        $table->double('marks_obtained',10,2)->nullable();
        $table->dateTime('completed_at')->nullable();

        $table->foreign('assignment_id')
            ->references('assignment_id')
            ->on('intern_curriculum_assignment')
            ->cascadeOnDelete();

        $table->foreign('cp_id')
            ->references('cp_id')
            ->on('curriculum_projects');
    });
       }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('intern_project_progress');
    }
};
