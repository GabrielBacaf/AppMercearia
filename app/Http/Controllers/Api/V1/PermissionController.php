<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\PermissionEnum;
use App\Http\Resources\V1\Permission\PermissionResource;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function index()
    {
        $this->authorize(PermissionEnum::INDEX->value);
        $permissions = Permission::all();
        return $this->successResponse(PermissionResource::collection($permissions), "Permissões listados com sucesso!", 200);
    }
}
