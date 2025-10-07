<?php

namespace App\Livewire\Dashboard;

use App\Models\NotificationAnalytic;
use Carbon\Carbon;
use Livewire\Component;

class NotificationAnalyticsFilter extends Component
{
    public string $filter = 'today';
    public ?string $startDate = null;
    public ?string $endDate = null;

    public function render()
    {
        $analytics = $this->getAnalytics();

        return view('livewire.dashboard.notification-analytics-filter', [
            'analytics' => $analytics,
            'totalSent' => $analytics->sum('total_sent'),
        ]);
    }

    public function updated($propertyName)
    {
        if ($propertyName === 'startDate' || $propertyName === 'endDate') {
            if ($this->startDate && $this->endDate && $this->filter === 'custom') {
                $this->render();
            }
        }
    }

    private function getAnalytics()
    {
        $query = NotificationAnalytic::with('notification');

        switch ($this->filter) {
            case 'total':

                break;

            case 'week':
                $query = $query->forWeek();
                break;

            case 'month':
                $query = $query->forMonth();
                break;

            case 'custom':
                $query = $query->forCustom($this->startDate, $this->endDate);
                break;

            case 'today': // today
                $query = $query->forDay(Carbon::now());
                break;
        }

        $allAnalytics = $query->get();

        $aggregatedAnalytics = $allAnalytics->groupBy('notification_id')->map(function ($dailyAnalytics) {
            $firstAnalytic = $dailyAnalytics->first();
            $totalSentSum = $dailyAnalytics->sum('total_sent');
            $summedChannels = [];

            foreach ($dailyAnalytics as $analytic) {
                $channels = is_string($analytic->channel_breakdown)
                    ? json_decode($analytic->channel_breakdown, true) ?? []
                    : ($analytic->channel_breakdown ?? []);

                foreach ($channels as $channel => $count) {
                    $summedChannels[$channel] = ($summedChannels[$channel] ?? 0) + $count;
                }
            }

            return [
                'notification' => $firstAnalytic->notification->title ?? 'N/A',
                'total_sent'   => $totalSentSum,
                'channels'     => $summedChannels,
            ];
        })->values(); // Reset keys

        return $aggregatedAnalytics;
    }
}
