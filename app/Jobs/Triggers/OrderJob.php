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

    public const CHANNEL_EMAIL = 'email';
    public const CHANNEL_SMS   = 'sms';
    public const CHANNEL_PUSH  = 'push';

    private const CHANNEL_MAP = [
        'email' => self::CHANNEL_EMAIL,
        'sms'   => self::CHANNEL_SMS,
        'push'  => self::CHANNEL_PUSH,
    ];

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
                $this->updateAnalytics($type, count($customers));
            }
        }
    }

    private function updateAnalytics(string $channelType, int $sentCount = 1): void
    {
        $channel = self::CHANNEL_MAP[$channelType] ?? $channelType;

        $analytic = $this->notification->analytics()
            ->firstOrCreate(
                ['date' => now()->toDateString()],
                ['total_sent' => 0, 'channel_breakdown' => []]
            );

        $analytic->total_sent += $sentCount;

        $breakdown = $analytic->channel_breakdown ?? [];
        $breakdown[$channel] = ($breakdown[$channel] ?? 0) + $sentCount;

        $analytic->channel_breakdown = $breakdown;
        $analytic->save();
    }
}
