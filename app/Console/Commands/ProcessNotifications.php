<?php

/**
 * ============================================
 * NOTIFICATION SYSTEM UPDATE - PHASE 3
 * ============================================
 * Date: 2026-04-18
 * 
 * CHANGES MADE:
 * - Created brand new ProcessNotifications command
 * 
 * REASON:
 * - Time-based notifications handle karne ke liye command chahiye
 * - 8-hour escalation rule check karne ke liye
 * - Internship completion check karne ke liye (end date reached)
 * - Ye command har 30 minutes mein schedule hogi
 * 
 * AFFECTED MODULES:
 * - EscalationService (8-hour rule)
 * - InternAccount (internship completion)
 * 
 * SCHEDULE: everyThirtyMinutes() in Kernel.php
 * ============================================
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\EscalationService;
use App\Models\InternAccount;
use Carbon\Carbon;

class ProcessNotifications extends Command
{
    protected $signature = 'notifications:process';
    protected $description = 'Process time-based notifications (escalations, completions)';
    
    public function handle(EscalationService $escalationService)
    {
        $this->info('📧 Processing time-based notifications...');
        $startTime = microtime(true);
        
        // ============================================
        // 1. Check 8-hour escalations (Interview + Test)
        // ============================================
        $this->info('  🔍 Checking 8-hour escalations...');
        
        $interviewCount = $escalationService->checkInterviewEscalations(8);
        $testCount = $escalationService->checkTestEscalations(8);
        
        $this->info("     ✓ Interview escalations: {$interviewCount}");
        $this->info("     ✓ Test escalations: {$testCount}");
        
        // Log escalation summary
        if ($interviewCount > 0 || $testCount > 0) {
            \Illuminate\Support\Facades\Log::info("Escalations processed", [
                'interview' => $interviewCount,
                'test' => $testCount,
                'timestamp' => now()
            ]);
        }
        
        // ============================================
        // 2. Check internship completions (end date reached)
        // ============================================
        $this->info('  🔍 Checking internship completions...');
        
        $today = now()->toDateString();
        
        // Find active interns whose end date is today
        $completedInterns = InternAccount::where('int_status', 'active')
            ->whereDate('end_date', $today)
            ->get();
        
        $completionCount = 0;
        
        foreach ($completedInterns as $intern) {
            // Update status to 'completed_pending' for manager approval
            $intern->update(['int_status' => 'completed_pending']);
            $completionCount++;
            
            $this->info("     ✓ Internship completion flagged: {$intern->name} (ID: {$intern->int_id})");
            
            // Log completion
            \Illuminate\Support\Facades\Log::info("Internship completion pending approval", [
                'intern_id' => $intern->int_id,
                'intern_name' => $intern->name,
                'end_date' => $intern->end_date
            ]);
            
            // Note: Notification to manager will be sent via UnifiedNotificationService
            // when manager views their dashboard or via separate method
        }
        
        $this->info("     ✓ Total completions flagged: {$completionCount}");
        
        // ============================================
        // 3. Summary
        // ============================================
        $executionTime = round((microtime(true) - $startTime) * 1000, 2);
        
        $this->info('✅ Processing complete!');
        $this->info("   📊 Summary:");
        $this->info("      - Interview escalations: {$interviewCount}");
        $this->info("      - Test escalations: {$testCount}");
        $this->info("      - Internship completions: {$completionCount}");
        $this->info("      - Execution time: {$executionTime}ms");
        
        return Command::SUCCESS;
    }
}