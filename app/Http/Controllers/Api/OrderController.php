<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\OrderCreateRequest;
use App\Http\Requests\Api\OrderUpdateRequest;
use App\Models\Customer;
use App\Models\Order;
use App\Services\Triggers\OrderService;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    /**
     * Class constructor.
     */
    public function __construct(private OrderService $orderService)
    {
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(OrderCreateRequest $request): JsonResponse
    {
        $data = $request->validated();
        $customer = Customer::where('profy_id', $data['customer_id'])->firstOrFail();
        $order = Order::create([
            'order_number' => $data['order_number'],
            'customer_id' => $customer->id
        ]);

        $this->orderService->orderCreatedJob($order);
        return response()->json([
            'message' => 'Order created successfully',
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(OrderUpdateRequest $request): JsonResponse
    {
        $data = $request->validated();
        $order = Order::where('order_number', $data['order_id'])->firstOrFail();
        $order->update([
            'finished_at' => date("Y-m-d H:i:s", $data['finished_at']),
            'price' => $data['price'],
            'type' => $data['type']
        ]);

        $this->orderService->orderCreateFinishedJob($order);
        return response()->json([
            'message' => 'Order updated successfully',
        ]);
    }
}
