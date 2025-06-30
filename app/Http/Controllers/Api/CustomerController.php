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

    /**
     * Update the specified resource in storage.
     */
    public function update(CustomerUpdateRequest $request): string
    {
        $data = $request->validated();
        $customer = Customer::where('profy_id', $data['profy_id'])->first();
        $customer->update($data);

        return response()->json(['message' => 'Customer updated'], 200);
    }
}
