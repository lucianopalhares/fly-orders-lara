<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('api')->group(function () {
    Route::prefix('orders')->group(function () {
        Route::post('/create', [OrderController::class, 'create']);
        Route::get('/show/{id}', [OrderController::class, 'get']);
        Route::get('/status/{id}', [OrderController::class, 'updateStatus']);
        Route::get('/list', [OrderController::class, 'getOrders']);
    });
});
