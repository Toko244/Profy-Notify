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

        $applyAggregation = function ($query) {
            return $query->selectRaw(
                'notification_id,
                SUM(total_sent) as total_sent,
                MAX(channel_breakdown) as channel_breakdown'
            )->groupBy('notification_id');
        };

        switch ($this->filter) {
            case 'total':
                $query = $applyAggregation($query);
                break;

            case 'week':
                $query = $applyAggregation(
                    $query->forWeek()
                );
                break;

            case 'month':
                $query = $applyAggregation(
                    $query->forMonth()
                );
                break;

            case 'custom':
                $query = $applyAggregation(
                    $query->forCustom($this->startDate, $this->endDate)
                );
                break;

            default: // today
                $query = $applyAggregation(
                    $query->forDay(Carbon::now())
                );
                break;
        }

        return $query->get()->map(function ($analytic) {
            return [
                'notification' => $analytic->notification->title ?? 'N/A',
                'total_sent'   => $analytic->total_sent,
                'channels'     => is_string($analytic->channel_breakdown)
                                    ? json_decode($analytic->channel_breakdown, true) ?? []
                                    : ($analytic->channel_breakdown ?? []),
            ];
        });
    }
}
