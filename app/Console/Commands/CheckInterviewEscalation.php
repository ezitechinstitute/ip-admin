<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Services\EscalationService;
use App\Models\EscalationTracking;

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
        $hours = (int) $this->option('hours');
        
        $this->info("🔍 Checking for escalations (> {$hours} hours without update)...");

        try {
            $escalationService = new EscalationService();

            // Check interview escalations
            $this->info("📋 Checking interviews...");
            $interviewEscalations = $escalationService->checkInterviewEscalations($hours);
            $this->line("   • {$interviewEscalations} interview(s) escalated");

            // Check test escalations
            $this->info("📝 Checking tests...");
            $testEscalations = $escalationService->checkTestEscalations($hours);
            $this->line("   • {$testEscalations} test(s) escalated");

            // Check for escalations that need upgrading to admin alert
            $this->info("⬆️  Checking for escalation upgrades...");
            $upgraded = $this->upgradeEscalations($hours);
            $this->line("   • {$upgraded} escalation(s) upgraded to admin alert");

            // Get summary
            $summary = $escalationService->getEscalationSummary();

            $this->info("\n📊 Escalation Summary:");
            $this->line("   • Total Pending: {$summary['total_pending']}");
            $this->line("   • Manager Reminders: {$summary['manager_reminders']}");
            $this->line("   • Admin Alerts: {$summary['admin_alerts']}");
            $this->line("   • Interview Escalations: {$summary['interview_escalations']}");
            $this->line("   • Test Escalations: {$summary['test_escalations']}");
            $this->line("   • Unnotified Admin: {$summary['unnotified_admin']}");

            $this->info("\n✅ Escalation check completed!\n");

            Log::info("Escalation check completed", [
                'hours' => $hours,
                'interviews' => $interviewEscalations,
                'tests' => $testEscalations,
                'upgraded' => $upgraded,
                'summary' => $summary,
            ]);

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error("❌ Error during escalation check: {$e->getMessage()}");
            Log::error("Escalation check failed: {$e->getMessage()}", [
                'exception' => $e,
            ]);
            return Command::FAILURE;
        }
    }

    /**
     * Upgrade escalations from manager_reminder to admin_alert
     */
    private function upgradeEscalations($hours): int
    {
        $escalationService = new EscalationService();
        $upgraded = 0;

        // Get all pending manager_reminder level escalations
        $escalations = EscalationTracking::pending()
            ->whereIn('escalation_level', ['manager_reminder'])
            ->get();

        foreach ($escalations as $escalation) {
            // If escalation has been at manager_reminder level for 8+ hours, upgrade
            $upgradeTime = $escalation->escalated_at->addHours($hours);
            
            if (now()->greaterThan($upgradeTime)) {
                if ($escalationService->upgradeEscalation($escalation)) {
                    $upgraded++;
                    $this->line("   → Escalation #{$escalation->id} upgraded to admin alert");
                }
            }
        }

        return $upgraded;
    }
}
