<?php

namespace App\Services;

use App\Models\EscalationTracking;
use App\Models\Intern;
use App\Models\InternAccount;
use App\Models\ManagersAccount;
use App\Models\AdminAccount;
use App\Notifications\EscalationCreatedNotification;
use App\Notifications\AdminEscalationNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Carbon\Carbon;

class EscalationService
{
    /**
     * Check for interns in Interview stage not updated within hours
     */
    public function checkInterviewEscalations($hours = 8)
    {
        $escalationTime = now()->subHours($hours);

        // Find interns in Interview status not updated for X hours
        $interns = Intern::where('status', 'interview')
            ->where('updated_at', '<', $escalationTime)
            ->get();

        $escalatedCount = 0;

        foreach ($interns as $intern) {
            if ($this->escalateInterview($intern, $hours)) {
                $escalatedCount++;
            }
        }

        return $escalatedCount;
    }

    /**
     * Check for interns in Test stage not updated within hours
     */
    public function checkTestEscalations($hours = 8)
    {
        $escalationTime = now()->subHours($hours);

        // Find interns in Test status not updated for X hours
        $interns = Intern::where('status', 'test')
            ->where('updated_at', '<', $escalationTime)
            ->get();

        $escalatedCount = 0;

        foreach ($interns as $intern) {
            if ($this->escalateTest($intern, $hours)) {
                $escalatedCount++;
            }
        }

        return $escalatedCount;
    }

    /**
     * Escalate a specific interview
     */
    public function escalateInterview($intern, $hours = 8): bool
    {
        try {
            // Check if already escalated and unresolved
            $existing = EscalationTracking::where('intern_id', $intern->id)
                ->where('escalation_type', 'interview')
                ->pending()
                ->first();

            if ($existing) {
                // Already escalated, check if we need to upgrade level
                if ($existing->isManagerReminder()) {
                    // Check if it's been X more hours since escalation
                    $upgradeTime = $existing->escalated_at->addHours($hours);
                    if (now()->greaterThan($upgradeTime)) {
                        return $this->upgradeEscalation($existing);
                    }
                }
                return false; // Already escalated
            }

            // Get related manager
            $manager = $intern->manager;
            if (!$manager) {
                Log::warning("No manager found for intern: {$intern->id}");
                return false;
            }

            // Create new escalation
            $escalation = EscalationTracking::create([
                'intern_id' => $intern->id,
                'manager_id' => $manager->manager_id,
                'escalation_type' => 'interview',
                'escalation_level' => 'manager_reminder',
                'escalated_at' => now(),
                'notes' => "Interview status not updated for {$hours} hours. Intern: {$intern->name}",
                'notified_admin' => false,
            ]);

            Log::info("Interview escalation created for intern: {$intern->name}", [
                'intern_id' => $intern->id,
                'escalation_id' => $escalation->id,
            ]);

            // Send notification to manager
            try {
                $notification = new EscalationCreatedNotification(
                    'interview',
                    $intern->name,
                    $intern->id,
                    $escalation->id
                );
                Notification::send($manager, $notification);
            } catch (\Exception $e) {
                Log::warning("Failed to send escalation notification: {$e->getMessage()}");
            }

            return true;
        } catch (\Exception $e) {
            Log::error("Error escalating interview: {$e->getMessage()}");
            return false;
        }
    }

    /**
     * Escalate a specific test
     */
    public function escalateTest($intern, $hours = 8): bool
    {
        try {
            // Check if already escalated and unresolved
            $existing = EscalationTracking::where('intern_id', $intern->id)
                ->where('escalation_type', 'test')
                ->pending()
                ->first();

            if ($existing) {
                // Already escalated, check if we need to upgrade level
                if ($existing->isManagerReminder()) {
                    // Check if it's been X more hours since escalation
                    $upgradeTime = $existing->escalated_at->addHours($hours);
                    if (now()->greaterThan($upgradeTime)) {
                        return $this->upgradeEscalation($existing);
                    }
                }
                return false; // Already escalated
            }

            // Get related manager
            $manager = $intern->manager;
            if (!$manager) {
                Log::warning("No manager found for intern: {$intern->id}");
                return false;
            }

            // Create new escalation
            $escalation = EscalationTracking::create([
                'intern_id' => $intern->id,
                'manager_id' => $manager->manager_id,
                'escalation_type' => 'test',
                'escalation_level' => 'manager_reminder',
                'escalated_at' => now(),
                'notes' => "Test status not updated for {$hours} hours. Intern: {$intern->name}",
                'notified_admin' => false,
            ]);

            Log::info("Test escalation created for intern: {$intern->name}", [
                'intern_id' => $intern->id,
                'escalation_id' => $escalation->id,
            ]);

            // Send notification to manager
            try {
                $notification = new EscalationCreatedNotification(
                    'test',
                    $intern->name,
                    $intern->id,
                    $escalation->id
                );
                Notification::send($manager, $notification);
            } catch (\Exception $e) {
                Log::warning("Failed to send escalation notification: {$e->getMessage()}");
            }

            return true;
        } catch (\Exception $e) {
            Log::error("Error escalating test: {$e->getMessage()}");
            return false;
        }
    }

    /**
     * Upgrade escalation from manager_reminder to admin_alert
     */
    public function upgradeEscalation(EscalationTracking $escalation): bool
    {
        try {
            $result = $escalation->upgradeToAdminAlert();

            if ($result) {
                Log::info("Escalation upgraded to admin_alert", [
                    'escalation_id' => $escalation->id,
                    'escalation_type' => $escalation->escalation_type,
                ]);

                // Send notification to admin after upgrade
                try {
                    $admins = AdminAccount::all();
                    $notification = new AdminEscalationNotification(
                        $escalation->escalation_type,
                        $escalation->intern?->name ?? 'Unknown',
                        $escalation->intern_id,
                        $escalation->manager?->name ?? 'Unknown',
                        $escalation->manager_id,
                        $escalation->getHoursSinceEscalation()
                    );
                    Notification::send($admins, $notification);
                } catch (\Exception $e) {
                    Log::warning("Failed to send admin escalation notification: {$e->getMessage()}");
                }
            }

            return $result;
        } catch (\Exception $e) {
            Log::error("Error upgrading escalation: {$e->getMessage()}");
            return false;
        }
    }

    /**
     * Resolve escalation
     */
    public function resolveEscalation(EscalationTracking $escalation, $notes = null): bool
    {
        try {
            return $escalation->resolve($notes);
        } catch (\Exception $e) {
            Log::error("Error resolving escalation: {$e->getMessage()}");
            return false;
        }
    }

    /**
     * Get all pending escalations
     */
    public function getPendingEscalations()
    {
        return EscalationTracking::pending()
            ->with(['intern', 'manager'])
            ->orderBy('escalated_at', 'asc')
            ->get();
    }

    /**
     * Get pending escalations by manager
     */
    public function getPendingEscalationsForManager($managerId)
    {
        return EscalationTracking::forManager($managerId)
            ->pending()
            ->with(['intern'])
            ->orderBy('escalated_at', 'asc')
            ->get();
    }

    /**
     * Get unnotified admin escalations
     */
    public function getUnnotifiedAdminEscalations()
    {
        return EscalationTracking::unnotifiedAdmin()
            ->with(['intern', 'manager'])
            ->orderBy('escalated_at', 'asc')
            ->get();
    }

    /**
     * Get escalations for dashboard
     */
    public function getEscalationSummary()
    {
        return [
            'total_pending' => EscalationTracking::pending()->count(),
            'manager_reminders' => EscalationTracking::pending()->managerReminder()->count(),
            'admin_alerts' => EscalationTracking::pending()->adminAlert()->count(),
            'interview_escalations' => EscalationTracking::pending()->interview()->count(),
            'test_escalations' => EscalationTracking::pending()->test()->count(),
            'unnotified_admin' => EscalationTracking::unnotifiedAdmin()->count(),
        ];
    }

    /**
     * Check if intern needs automatic resolution (status updated or moved to next stage)
     */
    public function checkAutoResolution($internId)
    {
        $intern = Intern::findOrFail($internId);

        // Get all pending escalations for this intern
        $escalations = EscalationTracking::where('intern_id', $internId)
            ->pending()
            ->get();

        foreach ($escalations as $escalation) {
            // If interview escalation and status moved from 'interview', resolve it
            if ($escalation->escalation_type === 'interview' && $intern->status !== 'interview') {
                $this->resolveEscalation($escalation, "Auto-resolved: Status changed to {$intern->status}");
            }

            // If test escalation and status moved from 'test', resolve it
            if ($escalation->escalation_type === 'test' && $intern->status !== 'test') {
                $this->resolveEscalation($escalation, "Auto-resolved: Status changed to {$intern->status}");
            }
        }
    }

    /**
     * Get escalation history for an intern
     */
    public function getInternEscalationHistory($internId)
    {
        return EscalationTracking::where('intern_id', $internId)
            ->with(['manager'])
            ->orderBy('escalated_at', 'desc')
            ->get()
            ->map(function ($escalation) {
                return $escalation->format();
            });
    }

    /**
     * Get escalation statistics
     */
    public function getStatistics($days = 30)
    {
        $from = now()->subDays($days);

        $stats = [
            'period_days' => $days,
            'total_escalations' => EscalationTracking::where('escalated_at', '>=', $from)->count(),
            'types' => [
                'interview' => EscalationTracking::interview()->where('escalated_at', '>=', $from)->count(),
                'test' => EscalationTracking::test()->where('escalated_at', '>=', $from)->count(),
            ],
            'levels' => [
                'manager_reminder' => EscalationTracking::managerReminder()->where('escalated_at', '>=', $from)->count(),
                'admin_alert' => EscalationTracking::adminAlert()->where('escalated_at', '>=', $from)->count(),
            ],
            'resolution_rate' => $this->calculateResolutionRate($from),
            'average_resolution_time' => $this->calculateAverageResolutionTime($from),
        ];

        return $stats;
    }

    /**
     * Calculate resolution rate
     */
    private function calculateResolutionRate($from): float
    {
        $escalations = EscalationTracking::where('escalated_at', '>=', $from)->get();

        if ($escalations->isEmpty()) {
            return 0;
        }

        $resolved = $escalations->filter(fn($e) => $e->isResolved())->count();
        $total = $escalations->count();

        return round(($resolved / $total) * 100, 2);
    }

    /**
     * Calculate average resolution time in hours
     */
    private function calculateAverageResolutionTime($from): float
    {
        $resolved = EscalationTracking::where('escalated_at', '>=', $from)
            ->whereNotNull('resolved_at')
            ->get();

        if ($resolved->isEmpty()) {
            return 0;
        }

        $avgHours = $resolved->map(function ($escalation) {
            return $escalation->escalated_at->diffInHours($escalation->resolved_at);
        })->average();

        return round($avgHours, 2);
    }
}
