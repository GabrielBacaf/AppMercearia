<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\PermissionController;

use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\PurchaseController;
use App\Http\Controllers\Api\V1\RoleController;
use App\Http\Controllers\Api\V1\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    Route::post('login', [AuthController::class, 'login'])->name('login');

    Route::middleware('auth:sanctum')->group(function () {

        Route::post('logout', [AuthController::class, 'logout'])->name('logout');
        Route::apiResource('users', UserController::class);
        Route::apiResource('roles', RoleController::class)->except('destroy');
        Route::apiResource('products', ProductController::class);
        Route::get('permissions', [PermissionController::class, 'index'])->name('permissions.index');
        Route::apiResource('purchases', PurchaseController::class);
    });
});
