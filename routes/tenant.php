<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/

Route::middleware([
    'api',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->prefix('api/v1')->group(function () {
    
    Route::post('login', [\App\Http\Controllers\Api\V1\AuthController::class, 'login'])->name('tenant.login');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [\App\Http\Controllers\Api\V1\AuthController::class, 'logout'])->name('tenant.logout');
        
        Route::apiResource('users', \App\Http\Controllers\Api\V1\UserController::class);
        Route::apiResource('roles', \App\Http\Controllers\Api\V1\RoleController::class)->except('destroy');
        Route::apiResource('products', \App\Http\Controllers\Api\V1\ProductController::class);
        Route::get('permissions', [\App\Http\Controllers\Api\V1\PermissionController::class, 'index'])->name('permissions.index');
        Route::apiResource('purchases', \App\Http\Controllers\Api\V1\PurchaseController::class);
        Route::apiResource('suppliers', \App\Http\Controllers\Api\V1\SupplierController::class)->except('destroy');
        Route::apiResource('clients', \App\Http\Controllers\Api\V1\ClientController::class)->except('destroy');
        Route::apiResource('sales', \App\Http\Controllers\Api\V1\SaleController::class);

        // Financial Module
        Route::prefix('financial')->group(function () {
            Route::apiResource('payables', \App\Http\Controllers\AccountPayableController::class)->only(['index', 'store']);
            Route::post('payables/{payable}/settle', [\App\Http\Controllers\AccountPayableController::class, 'settle'])->name('payables.settle');

            Route::apiResource('receivables', \App\Http\Controllers\AccountReceivableController::class)->only(['index', 'store']);
            Route::post('receivables/{receivable}/settle', [\App\Http\Controllers\AccountReceivableController::class, 'settle'])->name('receivables.settle');
        });
    });
});
