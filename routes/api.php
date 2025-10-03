<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login']);
// Route::post('/users', [UserController::class, 'store'])->name('users.store');


Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::apiResource('users', UserController::class);

    // Route::apiResource('products', App\Http\Controllers\Api\ProductController::class);
    // Route::apiResource('categories', App\Http\Controllers\Api\CategoryController::class);
    // Route::apiResource('clients', App\Http\Controllers\Api\ClientController::class);
    // Route::apiResource('orders', App\Http\Controllers\Api\OrderController::class);

});




