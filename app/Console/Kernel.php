<?php

namespace App\Console;

use App\Jobs\DeleteNotificationLogsJob;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Cache;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('notifications:dispatch-daily')->everyMinute();
        $schedule->command('notifications:dispatch-weekly')->everyMinute();
        $schedule->command('dispatch:monthly-notifications')->everyMinute();

        $schedule->job(new DeleteNotificationLogsJob)->everySixHours()->when(function() {
            return now()->diffInHours(Cache::get('last_notification_cleanup', now()->subHours(24))) >= 24;
        })->then(function() {
            Cache::put('last_notification_cleanup', now());
        });
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
