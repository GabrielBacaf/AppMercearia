<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::get('/user', fn(Request $request) => $request->user());


Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);

    // Route::apiResource('products', App\Http\Controllers\Api\ProductController::class);
    // Route::apiResource('categories', App\Http\Controllers\Api\CategoryController::class);
    // Route::apiResource('clients', App\Http\Controllers\Api\ClientController::class);
    // Route::apiResource('orders', App\Http\Controllers\Api\OrderController::class);
    // Route::post('orders/{order}/status', [App\Http\Controllers\Api\OrderController::class, 'updateStatus']);
});




