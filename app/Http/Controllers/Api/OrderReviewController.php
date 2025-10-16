<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\OrderReviewRequest;
use App\Models\OrderReview;

class OrderReviewController extends Controller
{
    public function store(OrderReviewRequest $request)
    {
        $data = $request->validated();

        OrderReview::create($data);

        return response()->json(['message' => 'Review created Successfully'], 201);
    }
}
