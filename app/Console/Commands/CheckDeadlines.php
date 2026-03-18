<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckDeadlines extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-deadlines';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = now()->toDateString();
        $this->info("Checking deadlines for $today...");

        // 1. Check Tasks
        $overdueTasks = \Illuminate\Support\Facades\DB::table('intern_tasks')
            ->where('task_end', '<', $today)
            ->whereNotIn('task_status', ['Completed', 'Approved', 'Expired'])
            ->get();

        foreach ($overdueTasks as $task) {
            \Illuminate\Support\Facades\DB::table('intern_tasks')
                ->where('task_id', $task->task_id)
                ->update([
                    'task_status' => 'Expired',
                    'penalty_flag' => 1,
                    'updated_at' => now()
                ]);

            // Notify Intern
            \Illuminate\Support\Facades\DB::table('supervisor_notifications')->insert([
                'supervisor_id' => $task->assigned_by,
                'type' => 'Task Expired',
                'eti_id' => $task->eti_id,
                'message' => "Your task '{$task->task_title}' has expired and a penalty flag has been added.",
                'is_read' => false,
                'created_at' => now()
            ]);
            
            $this->warn("Task ID {$task->task_id} expired.");
        }

        // 2. Check Projects
        $overdueProjects = \Illuminate\Support\Facades\DB::table('intern_projects')
            ->where('end_date', '<', $today)
            ->whereNotIn('pstatus', ['Completed', 'Approved', 'Expired'])
            ->get();

        foreach ($overdueProjects as $project) {
            \Illuminate\Support\Facades\DB::table('intern_projects')
                ->where('project_id', $project->project_id)
                ->update([
                    'pstatus' => 'Expired',
                    'updatedat' => now()
                ]);
            
            $this->warn("Project ID {$project->project_id} expired.");
        }

        $this->info('Deadline check completed.');
    }
}
