<?php

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

        $freezeService = new PortalFreezeService();
        $result = $freezeService->enforcePaymentFreeze();

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
