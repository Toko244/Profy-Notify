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
        $notifications = Notification::where('trigger', Trigger::REGISTER)->where('active', true)->get();
        $notifications->load('criteria');
        foreach ($notifications as $notification) {
            $delay = ($notification->additional['delay_m'] * 60) + ($notification->additional['delay_m'] * 3600) + ($notification->additional['delay_m'] * 86400);
            RegisterJob::dispatch($notification, $customer)->delay(now()->addSeconds($delay));
        }
    }
}
