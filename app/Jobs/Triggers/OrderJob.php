<?php

namespace App\Jobs\Triggers;

use App\Models\Customer;
use App\Models\Notification;
use App\Models\Order;
use App\Services\NotificationService;
use App\Services\QueryService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class OrderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private QueryService $queryService;
    private NotificationService $notificationService;
    /**
     * Create a new job instance.
     */
    public function __construct(
        public Notification $notification,
        public Order $order
    ){
        $this->queryService = new QueryService();
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $orderCheck = Order::query()->find($this->order->id);
        if ($this->notification->trigger === 'service_selected_not_ordered' && $orderCheck->status !== 'CREATED') {
            return;
        }

        $customers = $this->queryService->orderQuery($this->order, $this->notification);
        $notificationService = new NotificationService($this->notification, $customers);

        foreach ($this->notification->notification_type as $type) {
            if (method_exists($notificationService, $type)) {
                $notificationService->{$type}();
            }
        }
    }
}
