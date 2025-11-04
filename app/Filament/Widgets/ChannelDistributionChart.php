<?php

namespace App\Filament\Widgets;

use App\Models\NotificationAnalytic;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;

class ChannelDistributionChart extends ChartWidget
{
    protected static ?string $heading = '📊 Channel Distribution';

    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = [
        'md' => 2,
        'xl' => 1,
    ];

    public ?string $filter = 'week';

    protected static ?string $maxHeight = '288px';

    protected function getFilters(): ?array
    {
        return [
            'today' => 'Today',
            'week' => 'Last 7 Days',
            'month' => 'Last 30 Days',
        ];
    }

    protected function getData(): array
    {
        $startDate = match ($this->filter) {
            'today' => now()->startOfDay(),
            'week' => now()->subDays(7),
            'month' => now()->subDays(30),
            default => now()->subDays(7),
        };

        $analytics = NotificationAnalytic::whereBetween('date', [$startDate, now()])
            ->get();

        $totals = $analytics->reduce(function ($carry, $analytic) {
            $breakdown = $analytic->channel_breakdown ?? [];
            $carry['email'] += $breakdown['email'] ?? 0;
            $carry['sms'] += $breakdown['sms'] ?? 0;
            $carry['push'] += $breakdown['push'] ?? 0;
            return $carry;
        }, ['email' => 0, 'sms' => 0, 'push' => 0]);

        return [
            'datasets' => [
                [
                    'label' => 'Notifications by Channel',
                    'data' => array_values($totals),
                    'backgroundColor' => [
                        'rgba(59, 130, 246, 0.8)',   // Email - Blue
                        'rgba(16, 185, 129, 0.8)',   // SMS - Green
                        'rgba(251, 146, 60, 0.8)',   // Push - Orange
                    ],
                    'borderColor' => [
                        'rgb(59, 130, 246)',
                        'rgb(16, 185, 129)',
                        'rgb(251, 146, 60)',
                    ],
                    'borderWidth' => 2,
                ],
            ],
            'labels' => ['📧 Email', '💬 SMS', '📱 Push'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
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
                    'callbacks' => [
                        'label' => "function(context) {
                            let label = context.label || '';
                            let value = context.parsed || 0;
                            let total = context.dataset.data.reduce((a, b) => a + b, 0);
                            let percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                            return label + ': ' + new Intl.NumberFormat().format(value) + ' (' + percentage + '%)';
                        }",
                    ],
                ],
            ],
            'cutout' => '65%',
            'maintainAspectRatio' => false,
        ];
    }

    public function getDescription(): ?string
    {
        $startDate = match ($this->filter) {
            'today' => now()->startOfDay(),
            'week' => now()->subDays(7),
            'month' => now()->subDays(30),
            default => now()->subDays(7),
        };

        $total = NotificationAnalytic::whereBetween('date', [$startDate, now()])
            ->sum('total_sent');

        return "Total: **" . number_format($total) . "** notifications";
    }
}
