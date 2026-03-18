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
        Schema::create('intern_tasks', function (Blueprint $table) {
            $table->integer('task_id', true);
            $table->string('eti_id')->index('intenrkey');
            $table->string('task_title');
            $table->text('task_description');
            $table->string('task_start');
            $table->string('task_end');
            $table->integer('task_duration');
            $table->integer('task_days');
            $table->double('task_points');
            $table->double('task_obt_points');
            $table->integer('assigned_by')->index('tasksupkey');
            $table->string('task_status')->default('Ongoing');
            $table->boolean('task_approve')->nullable();
            $table->text('review');
            $table->longText('task_screenshot');
            $table->text('task_live_url');
            $table->text('task_git_url');
            $table->text('submit_description');
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrentOnUpdate()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('intern_tasks');
    }
};
