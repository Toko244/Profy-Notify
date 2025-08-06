<?php

namespace App\Services\Triggers;

use App\Enums\Trigger;
use App\Jobs\Triggers\OrderJob;
use App\Models\Notification;
use App\Models\NotificationLog;
use App\Models\Order;

class OrderService
{
    public function serviceSelectedNotOrderedJob(Order $order): void
    {
        $notifications = Notification::where('trigger', Trigger::SERVICE_SELECTED_NOT_ORDERED)->where('active', true)->get();
        $notifications->load('criteria');
        foreach ($notifications as $notification) {
            $existingLog = NotificationLog::where('notification_id', $notification->id)
                ->where('customer_id', $order->customer_id)
                ->where('trigger', Trigger::SERVICE_SELECTED_NOT_ORDERED)
                ->where('created_at', '>=', now()->subHours(24))
                ->first();

            if ($existingLog) {
                continue;
            }

            $delay = ($notification->additional['delay_m'] * 60) + ($notification->additional['delay_h'] * 3600) + ($notification->additional['delay_d'] * 86400);
            OrderJob::dispatch($notification, $order)->delay(now()->addSeconds($delay));

            NotificationLog::create([
                'notification_id' => $notification->id,
                'customer_id' => $order->customer_id,
                'trigger' => Trigger::SERVICE_SELECTED_NOT_ORDERED,
            ]);
        }
    }

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
        $notifications = Notification::where('trigger', Trigger::ORDER_FINISHED)->where('active', true)->get();
        $notifications->load('criteria');
        foreach ($notifications as $notification) {
            $delay = ($notification->additional['delay_m'] * 60) + ($notification->additional['delay_h'] * 3600) + ($notification->additional['delay_d'] * 86400);
            OrderJob::dispatch($notification, $order)->delay(now()->addSeconds($delay));
        }
    }
}
