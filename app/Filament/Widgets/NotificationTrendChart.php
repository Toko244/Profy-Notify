<?php

namespace App\Filament\Widgets;

use App\Models\NotificationAnalytic;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class NotificationTrendChart extends ChartWidget
{
    protected static ?string $heading = '📊 Notification Delivery Trends';

    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    public ?string $filter = 'week';

    protected static ?string $maxHeight = '300px';

    protected function getFilters(): ?array
    {
        return [
            'today' => 'Today',
            'week' => 'Last 7 Days',
            'month' => 'Last 30 Days',
            '3months' => 'Last 90 Days',
        ];
    }

    protected function getData(): array
    {
        $endDate = now();
        $startDate = match ($this->filter) {
            'today' => now()->startOfDay(),
            'week' => now()->subDays(7),
            'month' => now()->subDays(30),
            '3months' => now()->subDays(90),
            default => now()->subDays(7),
        };

        // Get analytics data grouped by date
        $analytics = NotificationAnalytic::whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'asc')
            ->get()
            ->groupBy(function ($item) {
                return Carbon::parse($item->date)->format('Y-m-d');
            });

        // Generate date range
        $dateRange = collect();
        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            $dateRange->push($currentDate->format('Y-m-d'));
            $currentDate->addDay();
        }

        // Prepare data arrays
        $labels = [];
        $emailData = [];
        $smsData = [];
        $pushData = [];
        $totalData = [];

        foreach ($dateRange as $date) {
            $dayAnalytics = $analytics->get($date, collect());

            $labels[] = $this->filter === 'today'
                ? Carbon::parse($date)->format('H:i')
                : Carbon::parse($date)->format('M d');

            $channels = $dayAnalytics->reduce(function ($carry, $analytic) {
                $breakdown = $analytic->channel_breakdown ?? [];
                $carry['email'] += $breakdown['email'] ?? 0;
                $carry['sms'] += $breakdown['sms'] ?? 0;
                $carry['push'] += $breakdown['push'] ?? 0;
                return $carry;
            }, ['email' => 0, 'sms' => 0, 'push' => 0]);

            $emailData[] = $channels['email'];
            $smsData[] = $channels['sms'];
            $pushData[] = $channels['push'];
            $totalData[] = $dayAnalytics->sum('total_sent');
        }

        return [
            'datasets' => [
                [
                    'label' => 'Total Sent',
                    'data' => $totalData,
                    'backgroundColor' => 'rgba(99, 102, 241, 0.1)',
                    'borderColor' => 'rgb(99, 102, 241)',
                    'borderWidth' => 3,
                    'fill' => true,
                    'tension' => 0.4,
                    'pointRadius' => 4,
                    'pointHoverRadius' => 6,
                    'pointBackgroundColor' => 'rgb(99, 102, 241)',
                ],
                [
                    'label' => '📧 Email',
                    'data' => $emailData,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'borderColor' => 'rgb(59, 130, 246)',
                    'borderWidth' => 2,
                    'fill' => true,
                    'tension' => 0.4,
                    'pointRadius' => 3,
                    'pointHoverRadius' => 5,
                ],
                [
                    'label' => '💬 SMS',
                    'data' => $smsData,
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'borderColor' => 'rgb(16, 185, 129)',
                    'borderWidth' => 2,
                    'fill' => true,
                    'tension' => 0.4,
                    'pointRadius' => 3,
                    'pointHoverRadius' => 5,
                ],
                [
                    'label' => '📱 Push',
                    'data' => $pushData,
                    'backgroundColor' => 'rgba(251, 146, 60, 0.1)',
                    'borderColor' => 'rgb(251, 146, 60)',
                    'borderWidth' => 2,
                    'fill' => true,
                    'tension' => 0.4,
                    'pointRadius' => 3,
                    'pointHoverRadius' => 5,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                    'labels' => [
                        'usePointStyle' => true,
                        'padding' => 15,
                        'font' => [
                            'size' => 12,
                            'weight' => 'bold',
                        ],
                    ],
                ],
                'tooltip' => [
                    'mode' => 'index',
                    'intersect' => false,
                    'backgroundColor' => 'rgba(0, 0, 0, 0.8)',
                    'padding' => 12,
                    'cornerRadius' => 8,
                    'titleFont' => [
                        'size' => 14,
                        'weight' => 'bold',
                    ],
                    'bodyFont' => [
                        'size' => 13,
                    ],
                    'bodySpacing' => 6,
                    'callbacks' => [
                        'label' => "function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed.y !== null) {
                                label += new Intl.NumberFormat().format(context.parsed.y) + ' sent';
                            }
                            return label;
                        }",
                    ],
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'precision' => 0,
                        'font' => [
                            'size' => 11,
                        ],
                    ],
                    'grid' => [
                        'color' => 'rgba(0, 0, 0, 0.05)',
                        'drawBorder' => false,
                    ],
                ],
                'x' => [
                    'grid' => [
                        'display' => false,
                        'drawBorder' => false,
                    ],
                    'ticks' => [
                        'font' => [
                            'size' => 11,
                        ],
                    ],
                ],
            ],
            'interaction' => [
                'mode' => 'nearest',
                'axis' => 'x',
                'intersect' => false,
            ],
            'maintainAspectRatio' => false,
        ];
    }

    public function getDescription(): ?string
    {
        $total = NotificationAnalytic::whereBetween('date', [
            match ($this->filter) {
                'today' => now()->startOfDay(),
                'week' => now()->subDays(7),
                'month' => now()->subDays(30),
                '3months' => now()->subDays(90),
                default => now()->subDays(7),
            },
            now(),
        ])->sum('total_sent');

        $period = match ($this->filter) {
            'today' => 'today',
            'week' => 'the last 7 days',
            'month' => 'the last 30 days',
            '3months' => 'the last 90 days',
            default => 'the last 7 days',
        };

        return "📤 **" . number_format($total) . "** notifications sent " . $period;
    }
}
