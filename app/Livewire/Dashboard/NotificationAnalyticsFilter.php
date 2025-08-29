<?php

namespace App\Livewire\Dashboard;

use App\Models\NotificationAnalytic;
use Carbon\Carbon;
use Livewire\Component;

class NotificationAnalyticsFilter extends Component
{
    public string $filter = 'today';

    public function render()
    {
        $analytics = $this->getAnalytics();

        return view('livewire.dashboard.notification-analytics-filter', [
            'analytics' => $analytics,
            'totalSent' => $analytics->sum('total_sent'),
        ]);
    }

    private function getAnalytics()
    {
        $query = NotificationAnalytic::with('notification');

        switch ($this->filter) {
            case 'week':
                $query->forWeek();
                break;
            case 'month':
                $query->forMonth();
                break;
            default:
                $query->forDay(Carbon::today());
                break;
        }

        return $query->get()->map(function ($analytic) {
            return [
                'notification' => $analytic->notification->title ?? 'N/A',
                'total_sent'   => $analytic->total_sent,
                'channels'     => $analytic->channel_breakdown ?? [],
            ];
        });
    }
}
