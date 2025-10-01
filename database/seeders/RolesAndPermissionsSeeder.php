<?php

namespace Database\Seeders;

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

    private function clearCachedPermissions(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }

    private function createPermissions(): void
    {
        $permissionEnums = [
            UserPermissionEnum::class,
            SalesPermissionEnum::class,

        ];

        foreach ($permissionEnums as $enumClass) {
            foreach ($enumClass::values() as $permission) {
                Permission::firstOrCreate([
                    'name' => $permission,
                    'guard_name' => $this->defaultGuard,
                ]);
            }
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
