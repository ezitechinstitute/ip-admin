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
        if (!Schema::hasTable('project_tasks')) {
            Schema::create('project_tasks', function (Blueprint $table) {
                $table->integer('task_id', true);
                $table->integer('project_id')->index('taskkey');
                $table->string('eti_id')->index('etikey1');
                $table->string('task_title');
                $table->string('t_start_date');
                $table->string('t_end_date');
                $table->integer('task_days');
                $table->integer('task_duration');
                $table->double('task_obt_mark');
                $table->double('task_mark');
                $table->integer('assigned_by')->index('assignby');
                $table->string('task_status')->default('Ongoing');
                $table->boolean('approved')->nullable();
                $table->text('review');
                $table->longText('task_screenshot');
                $table->text('task_live_url');
                $table->text('task_git_url');
                $table->text('description');
                $table->dateTime('created_at')->useCurrent();
                $table->dateTime('updated_at')->useCurrentOnUpdate()->useCurrent();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_tasks');
    }
};
