<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     */
    protected $commands = [
        // yahan apna custom command add karein
        \App\Console\Commands\SendInvoiceReminders::class,
        \App\Console\Commands\CheckInterviewEscalation::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule)
    {
        // Daily 9:00 AM pe invoice reminder bhejne ka scheduler
        $schedule->command('invoice:send-reminders')->dailyAt('09:00');

        // Check for interview/test escalations every hour (8-hour threshold)
        $schedule->command('interview:check-escalation --hours=8')->hourly();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}