<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\OrderController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/login', [AuthController::class, 'login']);

Route::prefix('customers')->group(function () {
    Route::get('/{profy_id}', [CustomerController::class, 'show']);
    Route::post('/', [CustomerController::class, 'store']);
    Route::put('/{profy_id}', [CustomerController::class, 'update']);
});

Route::prefix('orders')->group(function () {
    Route::post('/', [OrderController::class, 'store']);
    Route::put('/{order_number}', [OrderController::class, 'update']);
});
