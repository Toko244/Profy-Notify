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

class ScheduledJob implements ShouldQueue
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
        $customers = $this->queryService->customersQuery($this->notification);
        $notificationService = new NotificationService($this->notification, $customers);
        switch ($this->notification->notification_type) {
            case 'email':
                $notificationService->email();
                break;
            case 'sms':
                $notificationService->sms();
                break;
            default:
                # code...
                break;

            $notificationService->send($this->notification, $customers);
        }
    }
}
