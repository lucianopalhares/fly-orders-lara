<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\AuthController;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::get('profile', [AuthController::class, 'getProfile']);
    Route::get('notifications', [AuthController::class, 'notifications']);
    Route::post('logout', [AuthController::class, 'logout']);

    Route::prefix('orders')->group(function () {
        Route::post('/create', [OrderController::class, 'create']);
        Route::get('/show/{id}', [OrderController::class, 'get']);
        Route::get('/{id}/update-status', [OrderController::class, 'updateStatus']);
        Route::get('/list', [OrderController::class, 'getOrders']);
    });
});
