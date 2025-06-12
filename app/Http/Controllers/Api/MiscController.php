<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UserRegisteredRequest;
use App\Models\Customer;
use Illuminate\Http\Request;

class MiscController extends Controller
{
    public function userRegistered(UserRegisteredRequest $request)
    {
        $data = $request->validated();

        Customer::updateOrCreate(
            ['profy_id' => $data['id']],
            [
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'first_name' => $data['first_name'] ?? null,
                'last_name' => $data['last_name'] ?? null,
                'allow_notification' => !$data['disable_push_notification'],
                'onesignal_player_id' => $data['player_id'] ?? null,
            ]
        );

        return response()->json(['message' => 'User Saved']);
    }

    public function orderCreated(Request $request)
    {
        $data = $request->validate([
            'order_id' => 'required|integer',
            'user_id' => 'required|integer',
            'email' => 'required|email',
            'amount' => 'required|numeric',
        ]);

        return response()->json(['message' => 'Order notification sent']);
    }
}
