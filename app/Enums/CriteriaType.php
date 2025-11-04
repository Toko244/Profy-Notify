<?php

namespace App\Enums;

enum CriteriaType: string
{
    case HAS_ORDER              = 'has_order';
    case DOES_NOT_HAVE_ORDER    = 'does_not_have_order';
    case ORDER_NOT_COMPLETED    = 'order_not_completed';
    case ORDER_PRICE_MORE_THAN  = 'order_price_more_than';
    case ORDER_PRICE_LESS_THAN  = 'order_price_less_than';
    case MORE_THAN_ORDER_COUNT  = 'more_than_order_count';
    case LESS_THAN_ORDER_COUNT  = 'less_than_order_count';
    case ORDER_RATED_MORE_THAN  = 'order_rated_more_than';
    case ORDER_RATED_LESS_THAN  = 'order_rated_less_than';

    public function getLabel(): string
    {
        return match($this) {
            self::HAS_ORDER => 'Has Order',
            self::DOES_NOT_HAVE_ORDER => 'Does Not Have Order',
            self::ORDER_NOT_COMPLETED => 'Order Not Completed',
            self::ORDER_PRICE_MORE_THAN => 'Order Price More Than',
            self::ORDER_PRICE_LESS_THAN => 'Order Price Less Than',
            self::MORE_THAN_ORDER_COUNT => 'More Than Order Count',
            self::LESS_THAN_ORDER_COUNT => 'Less Than Order Count',
            self::ORDER_RATED_MORE_THAN => 'Order Rated More Than',
            self::ORDER_RATED_LESS_THAN => 'Order Rated Less Than',
        };
    }
}
