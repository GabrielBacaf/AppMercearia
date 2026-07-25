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

    // Rota de login para Super Admin
    Route::post('admin/login', [AuthController::class, 'login'])->name('central.login');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('admin/logout', [AuthController::class, 'logout'])->name('central.logout');

        // Gerenciamento de Tenants (Lojas) pelo Super Admin
        Route::apiResource('tenants', \App\Http\Controllers\Api\V1\Central\CentralTenantController::class);
    });
});
