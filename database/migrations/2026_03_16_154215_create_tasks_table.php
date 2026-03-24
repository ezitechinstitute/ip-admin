<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            
            // Foreign Keys - Match the exact column types
            $table->integer('supervisor_id'); // matches manager_id (int)
            $table->integer('intern_id');     // matches int_id (int)
            $table->integer('project_id')->nullable();
            
            // Task Details
            $table->date('deadline');
            $table->integer('points')->default(0);
            $table->enum('status', ['pending', 'submitted', 'approved', 'rejected', 'expired'])->default('pending');
            
            // Submission & Grading
            $table->text('submission_notes')->nullable();
            $table->text('supervisor_remarks')->nullable();
            $table->integer('grade')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            
            $table->timestamps();
            
            // Add foreign key constraints
            $table->foreign('supervisor_id')
                  ->references('manager_id')
                  ->on('manager_accounts')
                  ->onDelete('cascade');
                  
            $table->foreign('intern_id')
                  ->references('int_id')
                  ->on('intern_accounts')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('tasks');
    }
};