<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // 1. Drop the broken view if exists
        DB::statement('DROP VIEW IF EXISTS `tasks`');
        
        // 2. Drop existing tasks table if it exists (as view)
        if (Schema::hasTable('tasks')) {
            Schema::dropIfExists('tasks');
        }
        
        // 3. Create fresh tasks table
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('intern_id');
            $table->integer('supervisor_id')->nullable();
            $table->integer('project_id')->nullable();
            $table->date('deadline')->nullable();
            $table->integer('points')->default(0);
            $table->enum('status', ['pending', 'submitted', 'approved', 'rejected'])->default('pending');
            $table->text('github_url')->nullable();
            $table->text('live_url')->nullable();
            $table->text('submission_notes')->nullable();
            $table->text('supervisor_remarks')->nullable();
            $table->decimal('grade', 5, 2)->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
            
            // Indexes for performance
            $table->index('intern_id');
            $table->index('status');
            $table->index('deadline');
        });
        
        // 4. Migrate data from intern_tasks (if it exists)
        $internTasksExists = Schema::hasTable('intern_tasks');
        
        if ($internTasksExists) {
            DB::statement("
                INSERT INTO `tasks` (`title`, `description`, `intern_id`, `deadline`, `points`, `status`, `github_url`, `live_url`, `submission_notes`, `supervisor_remarks`, `created_at`, `updated_at`)
                SELECT 
                    it.`task_title`,
                    it.`task_description`,
                    (SELECT ia.`int_id` FROM `intern_accounts` ia WHERE ia.`eti_id` = it.`eti_id` LIMIT 1),
                    STR_TO_DATE(it.`task_end`, '%Y-%m-%d'),
                    it.`task_points`,
                    CASE 
                        WHEN it.`task_status` IN ('approved', 'Completed') THEN 'approved'
                        WHEN it.`task_status` = 'submitted' THEN 'submitted'
                        WHEN it.`task_status` = 'rejected' THEN 'rejected'
                        ELSE 'pending'
                    END,
                    it.`task_git_url`,
                    it.`task_live_url`,
                    it.`submit_description`,
                    it.`review`,
                    it.`created_at`,
                    it.`updated_at`
                FROM `intern_tasks` it
                WHERE (SELECT ia.`int_id` FROM `intern_accounts` ia WHERE ia.`eti_id` = it.`eti_id` LIMIT 1) IS NOT NULL
            ");
        }
        
        // 5. Drop duplicate tables (safe to delete)
        Schema::dropIfExists('intern_tasks');
        Schema::dropIfExists('project_tasks');
    }
    
    public function down()
    {
        // Rollback: Restore from backup if needed
        Schema::dropIfExists('tasks');
        
        
    }
};