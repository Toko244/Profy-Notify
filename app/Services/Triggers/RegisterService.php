<?php

namespace App\Services\Triggers;

use App\Enums\Trigger;
use App\Jobs\Triggers\RegisterJob;
use App\Models\Customer;
use App\Models\Notification;
use Illuminate\Support\Facades\Log;

class RegisterService
{
    public function createJob(Customer $customer): void
    {
        $notifications = Notification::where('trigger', Trigger::REGISTER)->where('active', true)->get();
        $notifications->load('criteria');
        foreach ($notifications as $notification) {
            $delay = ((int) ($notification->additional['delay_m'] ?? 0)) * 60
                    + ((int) ($notification->additional['delay_h'] ?? 0)) * 3600
                    + ((int) ($notification->additional['delay_d'] ?? 0)) * 86400;

            $runAt = now()->addSeconds($delay);
            Log::info("Dispatching RegisterJob. Delay: {$delay}s. Will run at: {$runAt}");
            RegisterJob::dispatch($notification, $customer)->delay($runAt);
        }
    }
}
