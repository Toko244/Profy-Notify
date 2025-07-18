<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CustomerCreateRequest;
use App\Http\Requests\Api\CustomerUpdateRequest;
use App\Models\Customer;
use App\Services\Triggers\RegisterService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CustomerController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(CustomerCreateRequest $request): JsonResponse
    {
        Log::info(json_encode($request->validated()));
        $customer = Customer::create($request->validated());
        $registerService = new RegisterService();
        $registerService->createJob($customer);

        return response()->json(['message' => 'Customer created'], 201);
    }

    public function show(int $profy_id): JsonResponse
    {
        $customer = Customer::where('profy_id', $profy_id)->firstOrFail();
        return response()->json($customer, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CustomerUpdateRequest $request): JsonResponse
    {
        $data = $request->validated();

        Customer::updateOrCreate(['profy_id' => $data['profy_id']], $data);

        return response()->json(['message' => 'Customer updated or created'], 200);
    }
}
