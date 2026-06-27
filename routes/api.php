<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\ClientController;
use App\Http\Controllers\Api\V1\EnumController;
use App\Http\Controllers\Api\V1\PermissionController;

use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\PurchaseController;
use App\Http\Controllers\Api\V1\RoleController;
use App\Http\Controllers\Api\V1\SaleController;
use App\Http\Controllers\Api\V1\SupplierController;
use App\Http\Controllers\Api\V1\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    Route::post('login', [AuthController::class, 'login'])->name('login');

    Route::middleware('auth:sanctum')->group(function () {

        Route::post('logout', [AuthController::class, 'logout'])->name('logout');
        
        Route::get('enums', [EnumController::class, 'index'])->name('enums.index');

        Route::apiResource('users', UserController::class);
        Route::apiResource('roles', RoleController::class)->except('destroy');
        Route::apiResource('products', ProductController::class);
        Route::get('permissions', [PermissionController::class, 'index'])->name('permissions.index');
        Route::apiResource('purchases', PurchaseController::class);
        Route::delete('purchases/{purchase}/products/{product}', [PurchaseController::class, 'removeProduct'])->name('purchases.products.destroy');
        Route::apiResource('suppliers', SupplierController::class)->except('destroy');
        Route::post('clients/extract-location', [ClientController::class, 'extractLocation'])->name('clients.extract-location');
        Route::apiResource('clients', ClientController::class)->except('destroy');
        Route::apiResource('sales', SaleController::class);
    });
});
