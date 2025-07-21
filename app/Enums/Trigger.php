<?php

namespace App\Enums;

enum Trigger: string{
    case REGISTER = 'register';
    case ORDER_CREATED = 'order_created';
    case ORDER_SERVICE_SELECTED_NOT_ORDERED = 'order_service_selected_not_ordered';
    case ORDER_FINISHED = 'order_finished';
    case DAILY = 'daily';
    case WEEKLY = 'weekly';
    case MONTHLY = 'monthly';
    case SCHEDULED = 'scheduled';
}
