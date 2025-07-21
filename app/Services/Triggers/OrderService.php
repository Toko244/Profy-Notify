<?php

namespace App\Services\Triggers;

use App\Enums\Trigger;
use App\Jobs\Triggers\OrderJob;
use App\Models\Notification;
use App\Models\Order;

class OrderService
{
    public function orderCreatedJob(Order $order): void
    {
        $notifications = Notification::where('trigger', Trigger::ORDER_CREATED)->where('active', true)->get();
        $notifications->load('criteria');
        foreach ($notifications as $notification) {
            $delay = ($notification->additional['delay_m'] * 60) + ($notification->additional['delay_h'] * 3600) + ($notification->additional['delay_d'] * 86400);
            OrderJob::dispatch($notification, $order)->delay(now()->addSeconds($delay));
        }
    }

    public function orderFinishedJob(Order $order): void
    {
        $notifications = Notification::where('trigger', Trigger::ORDER_CREATED)->where('active', true)->get();
        $notifications->load('criteria');
        foreach ($notifications as $notification) {
            $delay = ($notification->additional['delay_m'] * 60) + ($notification->additional['delay_h'] * 3600) + ($notification->additional['delay_d'] * 86400);
            OrderJob::dispatch($notification, $order)->delay(now()->addSeconds($delay));
        }
    }
}
