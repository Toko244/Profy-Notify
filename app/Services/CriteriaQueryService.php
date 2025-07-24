<?php

namespace App\Services;

use App\Enums\CriteriaType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;

class CriteriaQueryService
{
    protected $criteria = [];

    public function __construct()
    {
        $this->criteria = [
            'has_order' => 'hasOrder',
            'does_not_have_order' => 'doesNotHaveOrder',
            'order_not_completed' => 'orderNotCompleted',
            'order_price_more_than' => 'orderPriceMoreThan',
            'order_price_less_than' => 'orderPriceLessThan',
            'more_than_order_count' => 'moreThanOrderCount',
            'less_than_order_count' => 'lessThanOrderCount',
        ];
    }

    public function applyCustomerCriteria(Builder $query, array $criteria, $except = null)
    {
        foreach ($criteria as $criterion) {
            $key = $criterion['type'];
            if (isset($this->criteria[$key])) {
                $query = $this->{$this->criteria[$key]}($query, $criterion ?? [], $except);
            }
        }

        return $query;
    }

    public function hasOrder(Builder $query, $params, $except = null)
    {
        $orderFilter = function ($q) use ($params, $except) {
            $q->when(in_array($params['additional']['order_type'], ['handyman', 'cleaner']), function ($q) use ($params) {
                $q->where('type', ucfirst($params['additional']['order_type']));
            })->when(isset($params['additional']['duration']) && $params['additional']['duration'] > 0, function ($q) use ($params) {
                $q->where('created_at', '>=', Carbon::now()->subDays((int) $params['additional']['duration']));
            })->when($except, function ($q) use ($except) {
                $q->where('id', '!=', $except);
            });
        };

        return $query
            ->whereHas('orders', $orderFilter)
            ->when(isset($params['additional']['count']) && (int) $params['additional']['count'] > 0, function ($q) use ($orderFilter, $params) {
                $count = (int) $params['additional']['count'];
                $q->withCount(['orders as filtered_orders_count' => $orderFilter])
                ->having('filtered_orders_count', '=', $count);
            });
    }

    public function doesNotHaveOrder(Builder $query, $params, $except = null)
    {
        return $query->whereDoesntHave('orders', function ($query) use ($params, $except) {
            $query->where('created_at', '>=', Carbon::now()->subDays($params['additional']['duration']))
                ->when(in_array($params['additional']['order_type'], ['handyman', 'cleaner']), function ($q) use ($params) {
                    $q->where('type', ucfirst($params['additional']['order_type']));
                })
                ->when($except, function ($q) use ($except) {
                    $q->where('id', '!=', $except);
                });
        });
    }

    public function orderNotCompleted(Builder $query, $params, $except = null)
    {
        return $query->whereHas('orders', function ($query) use ($params, $except) {
            $query->where('created_at', '<=', Carbon::now()->subDays($params['additional']['duration']))
                ->where('status', '=', 'CREATED')
                ->whereNull('service_finished_at')
                ->when(in_array($params['additional']['order_type'], ['handyman', 'cleaner']), function ($q) use ($params) {
                    $q->where('type', ucfirst($params['additional']['order_type']));
                })
                ->when($except, function ($q) use ($except) {
                    $q->where('id', '!=', $except);
                });
        });
    }

    public function moreThanOrderCount(Builder $query, $params, $except = null)
    {
        return $query->withCount(['orders as filtered_orders_count' => function ($q) use ($params, $except) {
            $q->when(in_array($params['additional']['order_type'], ['handyman', 'cleaner']), function ($q) use ($params) {
                $q->where('type', ucfirst($params['additional']['order_type']));
            })->when(isset($params['additional']['duration']) && $params['additional']['duration'] > 0, function ($q) use ($params) {
                $q->where('created_at', '>=', Carbon::now()->subDays((int) $params['additional']['duration']));
            })->when($except, function ($q) use ($except) {
                $q->where('id', '!=', $except);
            });
        }])->having('filtered_orders_count', '>', (int) $params['additional']['count']);
    }

    public function lessThanOrderCount(Builder $query, $params, $except = null)
    {
        return $query->withCount(['orders as filtered_orders_count' => function ($q) use ($params, $except) {
            $q->when(in_array($params['additional']['order_type'], ['handyman', 'cleaner']), function ($q) use ($params) {
                $q->where('type', ucfirst($params['additional']['order_type']));
            })->when(isset($params['additional']['duration']) && $params['additional']['duration'] > 0, function ($q) use ($params) {
                $q->where('created_at', '>=', Carbon::now()->subDays((int) $params['additional']['duration']));
            })->when($except, function ($q) use ($except) {
                $q->where('id', '!=', $except);
            });
        }])->having('filtered_orders_count', '<', (int) $params['additional']['count']);
    }

    public function orderPriceMoreThan(Builder $query, $params, $except = null)
    {
        return $query->whereHas('orders', function ($query) use ($params, $except) {
            $query->where('price', '>=', (float) $params['additional']['price'])
                ->when(in_array($params['additional']['order_type'], ['handyman', 'cleaner']), function ($q) use ($params) {
                    $q->where('type', ucfirst($params['additional']['order_type']));
                })
                ->when($except, function ($q) use ($except) {
                    $q->where('id', '!=', $except);
                });
        });
    }

    public function orderPriceLessThan(Builder $query, $params, $except = null)
    {
        return $query->whereHas('orders', function ($query) use ($params, $except) {
            $query->where('price', '<=', (float) $params['additional']['price'])
                ->when(in_array($params['additional']['order_type'], ['handyman', 'cleaner']), function ($q) use ($params) {
                    $q->where('type', ucfirst($params['additional']['order_type']));
                })
                ->when($except, function ($q) use ($except) {
                    $q->where('id', '!=', $except);
                });
        });
    }
}
