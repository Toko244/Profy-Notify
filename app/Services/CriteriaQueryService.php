<?php

namespace App\Services;

use App\Enums\CriteriaType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

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
        return $query->whereHas('orders', function ($query) use ($params, $except) {
            $query->where('created_at', '>=', Carbon::now()->subDays($params['additional']['duration']))
                ->when(in_array($params['additional']['order_type'], ['handyman', 'cleaner']), function ($q) use ($params) {
                    $q->where('type', $params['additional']['order_type']);
                })
                ->when($except, function ($q) use ($except) {
                    $q->where('id', '!=', $except);
                });
        }, '=', $params['additional']['count'] ?? 1);
    }

    public function doesNotHaveOrder(Builder $query, $params, $except = null)
    {
        return $query->whereDoesntHave('orders', function ($query) use ($params, $except) {
            $query->where('created_at', '>=', Carbon::now()->subDays($params['additional']['duration']))
                ->when(in_array($params['additional']['order_type'], ['handyman', 'cleaner']), function ($q) use ($params) {
                    $q->where('type', $params['additional']['order_type']);
                })
                ->when($except, function ($q) use ($except) {
                    $q->where('id', '!=', $except);
                });
        });
    }

    public function orderNotCompleted(Builder $query, $params, $except = null)
    {
        return $query->whereHas('orders', function ($query) use ($params, $except) {
            $query->where('created_at', '<=', Carbon::now()->subMinute($params['additional']['duration']))
                ->whereNull('finished_at')
                ->when(in_array($params['additional']['order_type'], ['handyman', 'cleaner']), function ($q) use ($params) {
                    $q->where('type', $params['additional']['order_type']);
                })
                ->when($except, function ($q) use ($except) {
                    $q->where('id', '!=', $except);
                });
        });
    }

    public function orderPriceMoreThan(Builder $query, $params, $except = null)
    {
        return $query->whereHas('orders', function ($query) use ($params, $except) {
            $query->where('price', '>=', Carbon::now()->subDays($params['additional']['price']))
                ->when(in_array($params['additional']['order_type'], ['handyman', 'cleaner']), function ($q) use ($params) {
                    $q->where('type', $params['additional']['order_type']);
                })
                ->when($except, function ($q) use ($except) {
                    $q->where('id', '!=', $except);
                });
        });
    }

    public function orderPriceLessThan(Builder $query, $params, $except = null)
    {
        return $query->whereHas('orders', function ($query) use ($params, $except) {
            $query->where('price', '<=', Carbon::now()->subDays($params['additional']['price']))
                ->when(in_array($params['additional']['order_type'], ['handyman', 'cleaner']), function ($q) use ($params) {
                    $q->where('type', $params['additional']['order_type']);
                })
                ->when($except, function ($q) use ($except) {
                    $q->where('id', '!=', $except);
                });
        });
    }
}
