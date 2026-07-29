<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Tenant;
use Illuminate\Support\Facades\DB;

class TenantCreationTest extends TestCase
{
    protected bool $initializeTenancy = false;

    public function setUp(): void
    {
        parent::setUp();
        
        // Apaga o arquivo físico do banco de dados caso exista de uma execução anterior
        $dbPath = database_path('tenantmercearia-do-joao');
        if (file_exists($dbPath)) {
            unlink($dbPath);
        }
    }

    public function test_super_admin_can_create_new_tenant()
    {
        $superAdmin = \App\Models\User::factory()->create();
        
        $response = $this->actingAs($superAdmin, 'sanctum')->postJson('/api/v1/tenants', [
            'id' => 'mercearia-do-joao',
            'domain' => 'joao.erpmercearia.test',
            'admin_name' => 'João',
            'admin_login' => 'joao',
            'admin_email' => 'joao@mercearia.com',
            'admin_password' => 'password123',
        ]);

        $response->assertStatus(201)
                 ->assertJsonPath('message', 'Tenant criado com sucesso!')
                 ->assertJsonPath('data.id', 'mercearia-do-joao')
                 ->assertJsonPath('data.domains.0.domain', 'joao.erpmercearia.test');

        $this->assertDatabaseHas('tenants', [
            'id' => 'mercearia-do-joao'
        ]);

        $tenant = Tenant::find('mercearia-do-joao');
        
        $tenant->run(function () {
            $this->assertDatabaseHas('users', [
                'email' => 'joao@mercearia.com',
                'login' => 'joao',
                'name' => 'João'
            ]);
            
            // Verifica se o papel admin foi criado
            $this->assertDatabaseHas('roles', [
                'name' => 'admin',
                'guard_name' => 'api'
            ]);
            
            // Verifica se as permissões foram semeadas
            $this->assertDatabaseCount('permissions', count(array_merge(
                \App\Enums\Permissions\UserPermissionEnum::values(),
                \App\Enums\Permissions\RolePermissionEnum::values(),
                \App\Enums\Permissions\PermissionEnum::values(),
                \App\Enums\Permissions\ProductPermissionEnum::values(),
                \App\Enums\Permissions\PurchasePermissionEnum::values(),
                \App\Enums\Permissions\SupplierPermissionEnum::values(),
                \App\Enums\Permissions\ClientPermissionEnum::values(),
                \App\Enums\Permissions\SalePermissionEnum::values()
            )));
            
            // Verifica se o usuário tem o papel admin associado
            $user = \App\Models\User::where('email', 'joao@mercearia.com')->first();
            $this->assertTrue($user->hasRole('admin'));
        });
    }
}
