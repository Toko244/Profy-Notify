<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Notification;
use App\Models\Order;

class QueryService
{
    private CriteriaQueryService $criteriaQueryService;
    /**
     * Class constructor.
     */
    public function __construct(
    ){
        $this->criteriaQueryService = new CriteriaQueryService();
    }

    public function customerQuery(Customer $customer, Notification $notification): array
    {
        $notification->load('criteria');
        $query = Customer::where('id', $customer->id);
        $query = $this->criteriaQueryService->applyCustomerCriteria($query, $notification->criteria->toArray());
        return $query->get()->toArray();
    }

    public function customersQuery(Notification $notification): array
    {
        $notification->load('criteria');
        $query = Customer::query();
        $query = $this->criteriaQueryService->applyCustomerCriteria($query, $notification->criteria->toArray());
        return $query->get()->toArray();
    }

    public function orderQuery(Order $order, Notification $notification)
    {
        $notification->load('criteria');
        $query = Customer::whereHas('orders', function ($query) use ($order) {
            $query->where('id', $order->id);
        });
        $query = $this->criteriaQueryService->applyCustomerCriteria($query, $notification->criteria->toArray(), $order->id);
        return $query->get()->toArray();
    }
}
