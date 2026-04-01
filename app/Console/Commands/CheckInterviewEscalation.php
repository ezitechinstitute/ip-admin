<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Notifications\ManagerReminderNotification;
use App\Notifications\AdminEscalationNotification;

class CheckInterviewEscalation extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'interview:check-escalation {--hours=8 : Hours to wait before escalation}';

    /**
     * The console command description.
     */
    protected $description = 'Check for pending interviews/tests not updated in specified hours and escalate';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $hours = $this->option('hours');
        $escalationTime = now()->subHours($hours);

        $this->info("🔍 Checking for escalations (last $hours hours)...");

        // ==================== FAILED INTERVIEWS ====================
        
        $failedInterviews = DB::table('intern_table')
            ->where('status', 'Interview')
            ->whereNull('interview_completed_at')
            ->where('created_at', '<', $escalationTime)
            ->get();

        foreach ($failedInterviews as $intern) {
            $this->escalateInterview($intern, 'interview', $hours);
        }

        // ==================== FAILED TESTS ====================

        $failedTests = DB::table('intern_table')
            ->where('status', 'Test')
            ->where(function($q) {
                $q->where('test_status', '!=', 'completed')
                  ->orWhereNull('test_status');
            })
            ->whereNull('test_completed_at')
            ->where('created_at', '<', $escalationTime)
            ->get();

        foreach ($failedTests as $intern) {
            $this->escalateTest($intern, 'test', $hours);
        }

        $this->info('✅ Escalation check completed!');
    }

    /**
     * Escalate a pending interview
     */
    private function escalateInterview($intern, $type, $hours)
    {
        // Check if already escalated
        $existing = DB::table('escalation_tracking')
            ->where('intern_id', $intern->id)
            ->where('escalation_type', $type)
            ->whereNull('resolved_at')
            ->first();

        if ($existing) {
            // Upgrade escalation level if needed
            if ($existing->escalation_level === 'manager_reminder') {
                $this->upgradeEscalation($existing->id);
            }
            return;
        }

        // Get manager info
        $manager = DB::table('manager_accounts')
            ->where('manager_id', $intern->manager_id)
            ->first();

        if (!$manager) {
            $this->error("❌ Manager not found for intern: {$intern->id}");
            return;
        }

        // Create escalation record
        $escalation = DB::table('escalation_tracking')->insertGetId([
            'intern_id' => $intern->id,
            'manager_id' => $intern->manager_id,
            'escalation_type' => $type,
            'escalation_level' => 'manager_reminder',
            'escalated_at' => now(),
            'notes' => "Interview not updated for $hours hours. Intern: {$intern->name}",
            'notified_admin' => false,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Send notification to manager
        try {
            // Note: You need to ensure User model or create ManagerUser model
            // For now, we'll log the escalation
            $this->info("📋 Interview Escalation Created - Intern: {$intern->name}, Manager ID: {$intern->manager_id}");
        } catch (\Exception $e) {
            $this->error("Failed to send manager notification: " . $e->getMessage());
        }
    }

    /**
     * Escalate a pending test
     */
    private function escalateTest($intern, $type, $hours)
    {
        // Check if already escalated
        $existing = DB::table('escalation_tracking')
            ->where('intern_id', $intern->id)
            ->where('escalation_type', $type)
            ->whereNull('resolved_at')
            ->first();

        if ($existing) {
            // Upgrade escalation level if needed
            if ($existing->escalation_level === 'manager_reminder') {
                $this->upgradeEscalation($existing->id);
            }
            return;
        }

        // Get manager info
        $manager = DB::table('manager_accounts')
            ->where('manager_id', $intern->manager_id)
            ->first();

        if (!$manager) {
            $this->error("❌ Manager not found for intern: {$intern->id}");
            return;
        }

        // Create escalation record
        $escalation = DB::table('escalation_tracking')->insertGetId([
            'intern_id' => $intern->id,
            'manager_id' => $intern->manager_id,
            'escalation_type' => $type,
            'escalation_level' => 'manager_reminder',
            'escalated_at' => now(),
            'notes' => "Test not updated for $hours hours. Intern: {$intern->name}",
            'notified_admin' => false,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $this->info("📝 Test Escalation Created - Intern: {$intern->name}, Manager ID: {$intern->manager_id}");
    }

    /**
     * Upgrade escalation to admin alert level
     */
    private function upgradeEscalation($escalationId)
    {
        $escalation = DB::table('escalation_tracking')->find($escalationId);

        if (!$escalation) return;

        // Upgrade to admin alert
        DB::table('escalation_tracking')
            ->where('id', $escalationId)
            ->update([
                'escalation_level' => 'admin_alert',
                'notified_admin' => true,
                'updated_at' => now()
            ]);

        $this->warn("⚠️ Escalation upgraded to ADMIN ALERT - Escalation ID: {$escalationId}");
    }
}
