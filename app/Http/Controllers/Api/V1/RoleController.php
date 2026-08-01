<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\RolePermissionEnum;

use App\Http\Controllers\Api\V1\Controller;
use App\Http\Requests\Api\V1\Role\StoreRoleRequest;
use App\Http\Requests\Api\V1\Role\UpdateRoleRequest;
use App\Http\Requests\Api\V1\User\UpdateUserRequest;
use App\Http\Resources\V1\Role\RoleResource;
use App\Http\Services\RoleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use LaravelLang\Publisher\Console\Update;
use Spatie\Permission\Models\Role;
use Throwable;

class RoleController extends Controller
{

    public function __construct(protected RoleService $roleService)
    {
    }

    public function index(\Illuminate\Http\Request $request)
    {
        $this->authorize(RolePermissionEnum::INDEX->value);
        
        $roles = Role::latest()->paginate(5);
        return $this->successResponseCollection(
            RoleResource::collection($roles),
            $roles,
            "Perfis listados com sucesso!",
            200
        );
    }

    public function store(StoreRoleRequest $request)
    {
        $this->authorize(RolePermissionEnum::STORE->value);
        $role = $this->roleService->storeRole($request->validated());
        return $this->successResponse(
            new RoleResource($role),
            'Perfil criado com sucesso!',
            201
        );
    }

    public function update(UpdateRoleRequest $request, Role $role)
    {
        $this->authorize(RolePermissionEnum::UPDATE->value);

        $updatedRole = $this->roleService->updateRole($role, $request->validated());

        return $this->successResponse(
            new RoleResource($updatedRole),
            'Perfil atualizado com sucesso!',
            200
        );
    }

}
