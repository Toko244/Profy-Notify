<?php

namespace App\Jobs\Triggers;

use App\Models\Notification;
use App\Models\Order;
use App\Services\NotificationService;
use App\Services\QueryService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class OrderNotRatedJob implements ShouldQueue
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
        $customers = $this->queryService->orderNotRatedQuery($this->order, $this->notification);
        $notificationService = new NotificationService($this->notification, $customers);

        foreach ($this->notification->notification_type as $type) {
            if (method_exists($notificationService, $type)) {
                Log::info("Sending {$type} notification", [
                    'notification_id' => $this->notification->id,
                    'order_id'        => $this->order->id,
                    'customer_ids' => collect($customers)->pluck('id')->all(),
                    'channels'        => $this->notification->notification_type,
                ]);

                $sentCounts = $notificationService->{$type}();

                foreach ($sentCounts as $channelType => $sentCount) {
                    if ($sentCount > 0) {
                        $this->updateAnalytics($channelType, $sentCount);

                        Log::info('Notification sent', [
                            'notification_id' => $this->notification->id,
                            'order_id'        => $this->order->id,
                            'channel'         => $channelType,
                            'sent_count'      => $sentCount,
                            'customer_ids' => collect($customers)->pluck('id')->all(),
                        ]);
                    }
                }
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
