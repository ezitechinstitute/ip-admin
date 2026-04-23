<?php


/**
 * ============================================
 * NOTIFICATION SYSTEM UPDATE - PHASE 3
 * ============================================
 * Date: 2026-04-18
 * 
 * CHANGES MADE:
 * - Changed from 'new PortalFreezeService()' to 'app(PortalFreezeService::class)'
 * 
 * REASON:
 * - PortalFreezeService constructor mein UnifiedNotificationService chahiye
 * - 'new' keyword se instance banane par dependencies inject nahi hoti thin
 * - Laravel container (app() helper) automatically dependencies inject karta hai
 * - Without this fix, notification service null hota aur freeze notifications fail hoti thin
 * 
 * FIXES ISSUE: Dependency injection 
 * 
 * BEFORE: Direct instantiation - notification service missing
 * AFTER: Container resolution - all dependencies available
 * ============================================
 */

namespace App\Console\Commands;

use App\Services\PortalFreezeService;
use Illuminate\Console\Command;

class EnforcePortalFreeze extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'portal:freeze-overdue {--force : Force freeze check even if already run today}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for overdue invoices and freeze intern portals accordingly';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Starting Portal Freeze Enforcement Check...');

        
        // [FIXED] Changed: Use Laravel container instead of 'new' keyword
        // Reason: Container automatically injects UnifiedNotificationService dependency

           $freezeService = app(PortalFreezeService::class);
           $result = $freezeService->enforcePaymentFreeze();

       // $freezeService = new PortalFreezeService();
       // $result = $freezeService->enforcePaymentFreeze();

        if ($result['success']) {
            $this->info("✅ {$result['message']}");
            $this->line("Frozen Count: {$result['frozen_count']}");
            return Command::SUCCESS;
        } else {
            $this->error("❌ {$result['message']}");
            return Command::FAILURE;
        }
    }
}
