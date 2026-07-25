<?php

namespace Tests\Feature\Tenant;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Tenant;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UserAccessTest extends TestCase
{
    use RefreshDatabase;

    protected bool $initializeTenancy = false;

    public function test_tenant_user_can_access_tenant_routes()
    {
        $tenantId = 'minhaloja-' . uniqid();
        $tenant = Tenant::create(['id' => $tenantId]);
        $tenant->domains()->create(['domain' => $tenantId . '.test']);

        $user = null;

        $tenant->run(function () use (&$user) {
            $user = User::factory()->create([
                'email' => 'admin@minhaloja.test'
            ]);
            
            $role = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'api']);
            $user->assignRole($role);
        });

        $this->actingAs($user, 'sanctum');

        $url = 'http://' . $tenantId . '.test/api/v1/users';
        
        $response = $this->getJson($url);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
    }
}
