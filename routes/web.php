<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SymlinkController;
use App\Models\Customer;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


require __DIR__ . '/web/auth.php';
require __DIR__ . '/web/dashboard.php';

Route::get('testing', function (){
    $customer = Customer::first();

    $registerService = new \App\Services\Triggers\RegisterService();
    $registerService->createJob($customer);

    return 'done';
});

Route::get('/link/{any}', [SymlinkController::class, 'redirect'])->name('symlinks.redirect');
