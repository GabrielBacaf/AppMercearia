<?php

namespace Database\Seeders;

use App\Enums\PermissionEnum;
use App\Enums\RolePermissionEnum;
use App\Enums\SalesPermissionEnum;
use App\Enums\UserPermissionEnum;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    private string $defaultGuard = 'api';

    public function run(): void
    {
        $this->clearCachedPermissions();
        $this->createPermissions();
        $this->createAdminRole();
    }

    private function getAllEnumPermissions(): array
    {
        $permissionEnums = [
            UserPermissionEnum::class,
            RolePermissionEnum::class,
            PermissionEnum::class,
        ];

        $allPermissions = [];
        foreach ($permissionEnums as $enumClass) {
            $allPermissions = array_merge($allPermissions, $enumClass::values());
        }

        return $allPermissions;
    }

    private function clearCachedPermissions(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }

    private function createPermissions(): void
    {

        $allEnumPermissions = $this->getAllEnumPermissions();
        $this->deleteObsoletePermissions($allEnumPermissions);
        $this->createOrUpdatePermissions($allEnumPermissions);
    }

    private function deleteObsoletePermissions(array $validPermissions): void
    {
        $this->command->info('Limpando permissões obsoletas...');
        Permission::whereNotIn('name', $validPermissions)
            ->where('guard_name', $this->defaultGuard)
            ->delete();
    }
    private function createOrUpdatePermissions(array $validPermissions): void
    {
        $this->command->info('Criando ou atualizando permissões...');
        foreach ($validPermissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => $this->defaultGuard,
            ]);
        }
    }

    private function createAdminRole(): void
    {
        $roleName = 'admin';

        $role = Role::firstOrCreate([
            'name' => $roleName,
            'guard_name' => $this->defaultGuard,
        ]);

        $permissions = Permission::where('guard_name', $this->defaultGuard)->get();
        $role->syncPermissions($permissions);
    }

}
