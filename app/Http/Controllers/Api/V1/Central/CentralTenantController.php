<?php

namespace App\Http\Controllers\Api\V1\Central;

use App\Http\Controllers\Api\V1\Controller;
use App\Http\Requests\Api\V1\Central\StoreTenantRequest;
use App\Http\Requests\Api\V1\Central\UpdateTenantRequest;
use App\Http\Resources\V1\Central\TenantResource;
use App\Models\Tenant;
use App\Services\TenantService;

class CentralTenantController extends Controller
{
    protected TenantService $tenantService;

    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }

    public function index()
    {
        $tenants = Tenant::with('domains')->paginate(5);

        return $this->successResponseCollection(
            TenantResource::collection($tenants),
            $tenants,
            "Lojas listadas com sucesso!",
            200
        );
    }

    public function store(StoreTenantRequest $request)
    {
        $tenant = $this->tenantService->createTenant($request->validated());

        return $this->successResponse(
            new TenantResource($tenant->load('domains')),
            'Tenant criado com sucesso!',
            201
        );
    }

    public function show(Tenant $tenant)
    {
        return $this->successResponse(
            new TenantResource($tenant->load('domains')),
            'Tenant detalhado com sucesso!',
            200
        );
    }

    public function update(UpdateTenantRequest $request, Tenant $tenant)
    {
        $tenant = $this->tenantService->updateTenant($tenant, $request->validated());

        return $this->successResponse(
            new TenantResource($tenant->load('domains')),
            'Tenant atualizado com sucesso!',
            200
        );
    }

    public function destroy(Tenant $tenant)
    {
        $this->tenantService->deleteTenant($tenant);
        
        return $this->successResponse(
            null,
            'Tenant deletado com sucesso!',
            200
        );
    }
}

