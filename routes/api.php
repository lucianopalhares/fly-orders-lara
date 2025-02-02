<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Middleware\JwtMiddleware;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::get('profile', [AuthController::class, 'getProfile']);
    Route::post('logout', [AuthController::class, 'logout']);

    Route::prefix('orders')->group(function () {
        Route::post('/create', [OrderController::class, 'create']);
        Route::get('/show/{id}', [OrderController::class, 'get']);
        Route::get('/status/{id}', [OrderController::class, 'updateStatus']);
        Route::get('/list', [OrderController::class, 'getOrders']);
    });
});
