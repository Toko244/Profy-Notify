<?php

namespace App\Filament\Widgets;

use App\Models\Notification;
use App\Models\NotificationAnalytic;
use Carbon\Carbon;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\DB;

class TopNotificationsWidget extends BaseWidget
{
    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = [
        'md' => 2,
        'xl' => 1,
    ];

    protected static string $view = 'filament.widgets.top-notifications-widget';

    public ?string $filter = 'month';

    protected function getTableFilters(): array
    {
        return [];
    }

    protected function getFilters(): ?array
    {
        return [
            'week' => 'Last 7 Days',
            'month' => 'Last 30 Days',
            '3months' => 'Last 90 Days',
        ];
    }

    public function table(Table $table): Table
    {
        $startDate = match ($this->filter) {
            'week' => now()->subDays(7),
            'month' => now()->subDays(30),
            '3months' => now()->subDays(90),
            default => now()->subDays(30),
        };

        // Get aggregated data with proper ranking
        $topNotifications = NotificationAnalytic::query()
            ->whereBetween('date', [$startDate, now()])
            ->select('notification_id', DB::raw('SUM(total_sent) as total_sent'))
            ->groupBy('notification_id')
            ->orderByDesc('total_sent')
            ->get()
            ->map(function ($item, $index) {
                return [
                    'id' => $item->notification_id,
                    'rank' => $index + 1,
                    'notification_id' => $item->notification_id,
                    'total_sent' => $item->total_sent,
                ];
            });

        // Get notification details (including soft deleted)
        $notificationIds = $topNotifications->pluck('notification_id');
        $notifications = Notification::withTrashed()->whereIn('id', $notificationIds)->get()->keyBy('id');

        // Merge data and filter out deleted notifications
        $tableData = $topNotifications
            ->map(function ($item) use ($notifications) {
                $notification = $notifications->get($item['notification_id']);

                // Skip if notification doesn't exist at all (hard deleted or never existed)
                if (!$notification) {
                    return null;
                }

                return [
                    'id' => $item['notification_id'],
                    'rank' => $item['rank'],
                    'title' => $notification->title,
                    'total_sent' => $item['total_sent'],
                    'active' => $notification->active ?? false,
                    'deleted' => $notification->trashed(),
                ];
            })
            ->filter() // Remove null entries
            ->values() // Re-index
            ->map(function ($item, $index) {
                // Re-calculate ranks after filtering
                $item['rank'] = $index + 1;
                return $item;
            });

        return $table
            ->query(
                Notification::withTrashed()->whereIn('id', $tableData->pluck('id'))
            )
            ->modifyQueryUsing(function ($query) use ($tableData) {
                // Order by the rank we calculated
                $ids = $tableData->pluck('id')->implode(',');
                if ($ids) {
                    $query->orderByRaw("FIELD(id, $ids)");
                }
            })
            ->columns([
                Tables\Columns\TextColumn::make('rank')
                    ->label('#')
                    ->state(function ($record) use ($tableData) {
                        $item = $tableData->firstWhere('id', $record->id);
                        return $item['rank'] ?? '-';
                    })
                    ->badge()
                    ->color(fn ($state) => match(true) {
                        $state == 1 => 'warning',
                        $state <= 3 => 'success',
                        $state <= 5 => 'info',
                        default => 'gray',
                    })
                    ->icon(fn ($state) => match(true) {
                        $state == 1 => 'heroicon-o-trophy',
                        $state <= 3 => 'heroicon-o-star',
                        default => null,
                    })
                    ->size('lg')
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('title')
                    ->label('Notification')
                    ->searchable()
                    ->limit(35)
                    ->icon('heroicon-o-bell')
                    ->iconColor('primary')
                    ->weight('semibold')
                    ->description(function ($record) use ($tableData) {
                        $item = $tableData->firstWhere('id', $record->id);
                        if (!$item) return '';

                        if ($item['deleted'] ?? false) {
                            return '🗑️ Deleted';
                        }
                        return $item['active'] ? '✓ Active' : '⏸ Inactive';
                    }),
                Tables\Columns\TextColumn::make('total_sent')
                    ->label('Total Sent')
                    ->state(function ($record) use ($tableData) {
                        $item = $tableData->firstWhere('id', $record->id);
                        return $item['total_sent'] ?? 0;
                    })
                    ->alignEnd()
                    ->badge()
                    ->color('success')
                    ->formatStateUsing(fn ($state) => number_format($state))
                    ->icon('heroicon-o-paper-airplane')
                    ->size('lg'),
            ])
            ->paginated(false);
    }

    public function getFilterLabel(): string
    {
        return match ($this->filter) {
            'week' => 'Rankings based on last 7 days',
            'month' => 'Rankings based on last 30 days',
            '3months' => 'Rankings based on last 90 days',
            default => 'Rankings based on last 30 days',
        };
    }
}
