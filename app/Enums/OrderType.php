<?php

namespace App\Enums;

enum OrderType: string {
    case ANY        = 'any';
    case HANDYMAN   = 'handyman';
    case CLEANER    = 'cleaner';
}
