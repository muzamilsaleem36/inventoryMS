<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        
        // Run daily backup at 2 AM
        $schedule->command('pos:backup-database')->daily()->at('02:00');
        
        // Send low stock alerts every 6 hours
        $schedule->command('pos:send-low-stock-alerts')->everySixHours();
        
        // Generate daily reports at 6 AM
        $schedule->command('pos:generate-daily-reports')->dailyAt('06:00');
        
        // Clean up system files weekly
        $schedule->command('pos:cleanup-system')->weekly();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
} 