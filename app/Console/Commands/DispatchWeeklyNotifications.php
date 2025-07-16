<?php

namespace App\Console\Commands;

use App\Jobs\Triggers\WeeklyJob;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class DispatchWeeklyNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:dispatch-weekly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatch notifications that recur weekly at their scheduled day and time';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now('Asia/Tbilisi');
        $currentDay = strtolower($now->format('l'));
        $currentTime = $now->format('H:i');

        Notification::where('active', true)
            ->where('trigger', 'weekly')
            ->get()
            ->each(function ($notification) use ($currentDay, $currentTime) {
                $day = strtolower(data_get($notification->additional, 'week_day'));
                $time = data_get($notification->additional, 'time');

                if ($day === $currentDay && $time === $currentTime) {
                    dispatch(new WeeklyJob($notification));
                    Log::info("Dispatched weekly notification: {$notification->id} on {$day} at {$time}");
                }
            });
    }
}
