<?php

namespace App\Enums;

enum OrderStatus: string {
    case CREATED        = 'CREATED';
    case PAID           = 'PAID';
    case COMPLETED      = 'COMPLETED';
    case CANCELLED      = 'CANCELLED';
}
