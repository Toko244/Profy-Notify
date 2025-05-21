<?php

namespace App\Enums;

enum Trigger: string{
    case REGISTER                  = 'register';
    case ORDER_CREATED             = 'order_created';
    case ORDER_CREATE_FINISHED     = 'order_create_finished';
    case DAILY                     = 'daily';
    case WEEKLY                    = 'weekly';
    case MONTHLY                   = 'monthly';
    case SCHEDULED                 = 'scheduled';
}
