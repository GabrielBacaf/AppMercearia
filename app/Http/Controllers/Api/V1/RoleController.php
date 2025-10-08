<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\RolePermissionEnum;

use App\Http\Controllers\Api\V1\Controller;
use App\Http\Requests\Api\V1\Role\StoreRoleRequest;
use App\Http\Resources\Role\RoleResource;
use App\Http\Services\RoleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Contracts\Permission;
use Spatie\Permission\Models\Role;
use Throwable;

class RoleController extends Controller
{

    public function __construct(protected RoleService $roleService) {}

    public function index()
    {
        $this->authorize(RolePermissionEnum::INDEX->value);
        $roles = Role::all();
        return $this->successResponse(RoleResource::collection($roles), "Perfis listados com sucesso!", 200);
    }

    public function store(StoreRoleRequest $request)
    {
        $this->authorize(RolePermissionEnum::CREATE->value);
        try {
            $role = $this->roleService->storeRole($request->validated());
            return $this->successResponse(
                new RoleResource($role),
                'Perfil criado com sucesso!',
                201
            );
        } catch (Throwable $e) {
            Log::error('Erro ao criar perfil', ['exception' => $e]);
            return $this->errorResponse( $e->getMessage(), [] , 500);
        }
    }


    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
