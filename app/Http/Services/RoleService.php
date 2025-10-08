<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

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
            $role = Role::create([
                'name' => $data['name'],
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
    public function update(Role $role, array $data): Role
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
