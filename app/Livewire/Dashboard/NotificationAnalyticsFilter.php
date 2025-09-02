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

            default:
                $query->forDay(Carbon::now());
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
