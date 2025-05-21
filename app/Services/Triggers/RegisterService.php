<?php

namespace App\Services\Triggers;

use App\Enums\Trigger;
use App\Jobs\Triggers\RegisterJob;
use App\Models\Customer;
use App\Models\Notification;

class RegisterService
{
    public function createJob(Customer $customer): void
    {
        $notifications = Notification::where('trigger', Trigger::REGISTER)->get();
        $notifications->load('criteria');
        foreach ($notifications as $notification) {
            $delay = ($notification->delay_m * 60) + ($notification->delay_h * 3600) + ($notification->delay_d * 86400);
            RegisterJob::dispatch($notification, $customer)->delay(now()->addSeconds($delay));
        }
    }
}
