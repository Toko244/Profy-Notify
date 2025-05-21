<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationCategoryController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SegmentController;
use App\Http\Controllers\SymlinkController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Dashboard Routes
|--------------------------------------------------------------------------
*/

Route::group(['middleware' => 'auth'], function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('users', CustomerController::class)->only('index')->names('customers');

    Route::resource('segments', SegmentController::class)->names('segments');

    Route::resource('notifications', NotificationController::class)->names('notifications');

    Route::resource('symlinks', SymlinkController::class)->names('symlinks');

    Route::resource('notification-categories', NotificationCategoryController::class)->names('notification-categories');
});
