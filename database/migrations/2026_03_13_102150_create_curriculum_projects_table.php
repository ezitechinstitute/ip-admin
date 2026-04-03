<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('curriculum_projects')) {
            Schema::create('curriculum_projects', function (Blueprint $table) {
                $table->id('cp_id');

                $table->unsignedBigInteger('curriculum_id'); 
                $table->foreign('curriculum_id')
                      ->references('curriculum_id')
                      ->on('technology_curriculum')
                      ->onDelete('cascade');

                $table->string('project_title');
                $table->text('project_description');
                $table->integer('sequence_order');
                $table->integer('duration_weeks');

                // Fix: match manager_accounts.manager_id type exactly
                // $table->integer('assigned_supervisor')->nullable();
                $table->unsignedBigInteger('assigned_supervisor')->nullable();
                $table->foreign('assigned_supervisor')
                      ->references('manager_id')
                      ->on('manager_accounts')
                      ->onDelete('set null');

                $table->text('learning_objectives')->nullable();
                $table->text('deliverables')->nullable();
                $table->boolean('status')->default(1);

                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('curriculum_projects');
    }
};