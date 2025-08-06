<?php

namespace App\Enums;

enum Trigger: string{
    case REGISTER = 'register';
    case ORDER_CREATED = 'order_created';
    case ORDER_FINISHED = 'order_finished';
    case SERVICE_SELECTED_NOT_ORDERED = 'service_selected_not_ordered';
    case DAILY = 'daily';
    case WEEKLY = 'weekly';
    case MONTHLY = 'monthly';
    case SCHEDULED = 'scheduled';
}
