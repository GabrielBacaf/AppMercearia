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
            'admin_email' => 'joao@mercearia.com',
            'admin_password' => 'password123',
        ]);

        $response->dump();
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
                'name' => 'João'
            ]);
        });
    }
}
