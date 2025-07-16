<?php

namespace App\Console\Commands;

use App\Jobs\Triggers\MonthlyJob;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class DispatchMonthlyNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dispatch:monthly-notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatch notifications that recur monthly at their scheduled day and time';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now('Asia/Tbilisi');
        $currentDay = $now->day;
        $currentTime = $now->format('H:i');

        Notification::where('active', true)
            ->where('trigger', 'monthly')
            ->get()
            ->each(function ($notification) use ($currentDay, $currentTime) {
                $day = strtolower(data_get($notification->additional, 'day'));
                $time = data_get($notification->additional, 'time');

                if ($day == $currentDay && $time === $currentTime) {
                    dispatch(new MonthlyJob($notification));
                    Log::info("Dispatched monthly notification: {$notification->id} on day {$day} at {$time}");
                }
            });
    }
}
