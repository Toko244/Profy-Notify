<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Notification;
use App\Models\NotificationAnalytic;
use App\Models\Order;

class DashboardController extends Controller
{
    public function index()
    {
        $total_customers        = Customer::count();
        $total_orders           = Order::count();
        $active_orders          = Order::whereNull('service_finished_at')->count();
        $finished_orders        = Order::whereNotNull('service_finished_at')->count();
        $total_notifications    = Notification::count();
        $active_notifications   = Notification::where('active', true)->count();
        $inactive_notifications = Notification::where('active', false)->count();

        $analytics = NotificationAnalytic::with('notification')
            ->get()
            ->map(function ($analytic) {
                return [
                    'notification' => $analytic->notification->title ?? 'N/A',
                    'total_sent'   => $analytic->total_sent,
                    'channels'     => $analytic->channel_breakdown ?? [],
                ];
            });

        return view('pages.dashboard.index', compact(
            'total_customers',
            'total_orders',
            'active_orders',
            'finished_orders',
            'total_notifications',
            'active_notifications',
            'inactive_notifications',
            'analytics'
        ));
    }
}
