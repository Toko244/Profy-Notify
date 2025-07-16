<?php

namespace App\Jobs\Triggers;

use App\Models\Customer;
use App\Models\Notification;
use App\Services\NotificationService;
use App\Services\QueryService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class DailyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private QueryService $queryService;
    private NotificationService $notificationService;
    /**
     * Create a new job instance.
     */
    public function __construct(
        public Notification $notification
    ){
        $this->queryService = new QueryService();
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("Processing daily notification: {$this->notification->id}");
        $customers = $this->queryService->customersQuery($this->notification);
        $notificationService = new NotificationService($this->notification, $customers);

        foreach ($this->notification->notification_type as $type) {
            if (method_exists($notificationService, $type)) {
                $notificationService->{$type}();
            }
        }
    }
}
