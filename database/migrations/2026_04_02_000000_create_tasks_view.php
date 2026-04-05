<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Try to drop if exists - simple approach
        try {
            DB::statement("DROP VIEW IF EXISTS tasks");
        } catch (\Exception $e) {
            // Ignore error
        }
        
        try {
            DB::statement("DROP TABLE IF EXISTS tasks");
        } catch (\Exception $e) {
            // Ignore error
        }
        
        // Create the view
        DB::statement("
            CREATE VIEW tasks AS
            SELECT 
                t.task_id as id,
                t.task_title as title,
                t.task_description as description,
                t.assigned_by as supervisor_id,
                i.int_id as intern_id,
                NULL as project_id,
                STR_TO_DATE(t.task_end, '%Y-%m-%d') as deadline,
                COALESCE(t.task_points, 0) as points,
                CASE 
                    WHEN t.task_status IN ('approved', 'completed', 'Completed') THEN 'approved'
                    WHEN t.task_status = 'rejected' THEN 'rejected'
                    WHEN t.task_status = 'submitted' THEN 'submitted'
                    ELSE 'pending'
                END as status,
                t.submit_description as submission_notes,
                t.review as supervisor_remarks,
                t.grade as grade,
                CASE 
                    WHEN t.task_status = 'submitted' THEN t.updated_at 
                    ELSE NULL 
                END as submitted_at,
                CASE 
                    WHEN t.task_status IN ('approved', 'rejected') THEN t.updated_at 
                    ELSE NULL 
                END as reviewed_at,
                t.created_at,
                t.updated_at
            FROM intern_tasks t
            LEFT JOIN intern_accounts i ON t.eti_id = i.eti_id
        ");
    }

    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS tasks");
    }
};