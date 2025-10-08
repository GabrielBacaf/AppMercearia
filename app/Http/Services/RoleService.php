<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Throwable;

class RoleService
{

    /**
     * Cria um novo perfil (role) com permissões.
     *
     * @throws Throwable
     */
    public function storeRole(array $data): Role
    {
        return DB::transaction(function () use ($data) {
            $role = Role::firstOrCreate([
                'name' => $data['name'],
                'guard_name' => 'api',
            ]);

            $role->syncPermissions($data['permissions']);

            return $role;
        });
    }


    /**
     * Atualiza um perfil existente.
     *
     * @throws Throwable
     */
    public function updateRole(Role $role, array $data): Role
    {
        return DB::transaction(function () use ($role, $data) {
            $role->update([
                'name' => $data['name'],
            ]);

            $role->syncPermissions($data['permissions']);

            return $role;
        });
    }
}
