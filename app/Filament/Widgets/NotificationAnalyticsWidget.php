<?php

namespace App\Filament\Widgets;

use App\Models\NotificationAnalytic;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Widgets\Widget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class NotificationAnalyticsWidget extends Widget
{
    use InteractsWithPageFilters;

    protected static string $view = 'filament.widgets.notification-analytics-widget';

    protected int | string | array $columnSpan = 'full';

    public string $filter = 'today';
    public ?string $startDate = null;
    public ?string $endDate = null;

    protected function getViewData(): array
    {
        $analytics = $this->getAnalytics();

        return [
            'analytics' => $analytics,
            'totalSent' => $analytics->sum('total_sent'),
        ];
    }

    private function getAnalytics()
    {
        $query = NotificationAnalytic::with('notification');

        switch ($this->filter) {
            case 'total':
                break;

            case 'week':
                $query = $query->where('date', '>=', now()->subWeek());
                break;

            case 'month':
                $query = $query->where('date', '>=', now()->subMonth());
                break;

            case 'custom':
                if ($this->startDate && $this->endDate) {
                    $query = $query->whereBetween('date', [$this->startDate, $this->endDate]);
                }
                break;

            case 'today':
            default:
                $query = $query->whereDate('date', today());
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
        })->values();

        return $aggregatedAnalytics;
    }
}
