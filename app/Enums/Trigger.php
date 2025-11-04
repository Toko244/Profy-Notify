<?php

namespace App\Enums;

enum Trigger: string{
    case REGISTER = 'register';
    case ORDER_CREATED = 'order_created';
    case ORDER_FINISHED = 'order_finished';
    case SERVICE_SELECTED_NOT_ORDERED = 'service_selected_not_ordered';
    case ORDER_NOT_RATED = 'order_not_rated';
    case ORDER_RATED = 'order_rated';
    case DAILY = 'daily';
    case WEEKLY = 'weekly';
    case MONTHLY = 'monthly';
    case SCHEDULED = 'scheduled';

    public function getLabel(): string
    {
        return match($this) {
            self::REGISTER => 'User Registration',
            self::ORDER_CREATED => 'Order Created',
            self::ORDER_FINISHED => 'Order Finished',
            self::SERVICE_SELECTED_NOT_ORDERED => 'Service Selected but Not Ordered',
            self::ORDER_NOT_RATED => 'Order Not Rated',
            self::ORDER_RATED => 'Order Rated',
            self::DAILY => 'Daily',
            self::WEEKLY => 'Weekly',
            self::MONTHLY => 'Monthly',
            self::SCHEDULED => 'Scheduled',
        };
    }
}
