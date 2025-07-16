<?php

namespace App\Console\Commands;

use App\Jobs\Triggers\DailyJob;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class DispatchDailyNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:dispatch-daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatch notifications that recur daily at their scheduled time';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now('Asia/Tbilisi')->format('H:i');

        Notification::where('active', true)
            ->where('trigger', 'daily')
            ->get()
            ->each(function ($notification) use ($now) {
                $time = data_get($notification->additional, 'time');

                if ($time && $time === $now) {
                    dispatch(new DailyJob($notification));
                    Log::info("Dispatched daily notification: {$notification->id} at {$now}");
                }
            });
    }
}
