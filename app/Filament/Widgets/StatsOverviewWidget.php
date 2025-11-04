<?php

namespace App\Filament\Widgets;

use App\Models\Customer;
use App\Models\Notification;
use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $totalCustomers = Customer::count();
        $totalOrders = Order::count();
        $activeOrders = Order::whereNull('service_finished_at')->count();
        $finishedOrders = Order::whereNotNull('service_finished_at')->count();
        $totalNotifications = Notification::count();
        $activeNotifications = Notification::where('active', true)->count();
        $inactiveNotifications = Notification::where('active', false)->count();

        return [
            Stat::make('Total Customers', number_format($totalCustomers))
                ->description('All registered customers')
                ->descriptionIcon('heroicon-o-users')
                ->color('success'),

            Stat::make('Total Orders', number_format($totalOrders))
                ->description("Active: {$activeOrders} | Finished: {$finishedOrders}")
                ->descriptionIcon('heroicon-o-shopping-cart')
                ->color('info')
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                ]),

            Stat::make('Total Notifications', number_format($totalNotifications))
                ->description("Active: {$activeNotifications} | Inactive: {$inactiveNotifications}")
                ->descriptionIcon('heroicon-o-bell')
                ->color('warning'),
        ];
    }
}
