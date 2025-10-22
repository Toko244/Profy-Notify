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
            'customer_id' => $customer->id,
            'service_finished_at' => $data['service_finished_at'] ?? null,
            'price' => $data['price'] ?? null,
            'type' => $data['type'],
            'status' => $data['status'],
            'created_at' => $data['created_at'],
        ]);

        $this->orderService->serviceSelectedNotOrderedJob($order);

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
        $order = Order::where('order_number', $data['order_number'])->firstOrFail();
        $order->update($data);

        match ($order->status) {
            'PAID' => $this->orderService->orderCreatedJob($order),
            'WAITING_CONFIRMATION' => $this->orderService->orderNotRatedJob($order),
            'COMPLETED' => [
                $this->orderService->orderFinishedJob($order),
                $this->orderService->orderRatedJob($order),
            ],
            default => null,
        };

        return response()->json([
            'message' => 'Order updated successfully',
        ]);
    }
}
