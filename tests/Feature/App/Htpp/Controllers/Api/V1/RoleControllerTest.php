<?php

namespace Tests\Feature\App\Htpp\Controllers\Api\V1;

use App\Enums\RolePermissionEnum;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Database\Factories\RoleFactory;
use Tests\TestCase;

# php artisan test --filter=RoleControllerTest
class RoleControllerTest extends TestCase
{
    # php artisan test --filter=RoleControllerTest::test_index_deve_retornar_erro_403_se_nao_autorizado
    public function test_index_deve_retornar_erro_403_se_nao_autorizado(): void
    {
        $response = $this->withHeader('Authorization', "Bearer $this->token")
            ->getJson(route('roles.index'));

        $response->assertStatus(403);
    }

    # php artisan test --filter=RoleControllerTest::test_index_deve_listar_perfis_com_sucesso
    public function test_index_deve_listar_perfis_com_sucesso(): void
    {
        $this->user->givePermissionTo(RolePermissionEnum::INDEX->value);
        RoleFactory::new()->count(3)->create(['guard_name' => 'api']);

        $response = $this->withHeader('Authorization', "Bearer $this->token")
            ->getJson(route('roles.index'));

        $response->assertStatus(200)
            ->assertJsonCount(4, 'data');
    }

    # php artisan test --filter=RoleControllerTest::test_store_deve_retornar_erro_403_se_nao_autorizado
    public function test_store_deve_retornar_erro_403_se_nao_autorizado(): void
    {
        $roleData = ['name' => 'New Role', 'permissions' => []];
        $response = $this->withHeader('Authorization', "Bearer $this->token")
            ->postJson(route('roles.store'), $roleData);
        $response->assertStatus(403);
    }

    # php artisan test --filter=RoleControllerTest::test_store_deve_retornar_erro_de_validacao_com_dados_invalidos
    public function test_store_deve_retornar_erro_de_validacao_com_dados_invalidos(): void
    {
        $this->user->givePermissionTo(RolePermissionEnum::STORE->value);
        $invalidData = ['permissions' => 'not-an-array'];

        $response = $this->withHeader('Authorization', "Bearer $this->token")
            ->postJson(route('roles.store'), $invalidData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'permissions']);
    }

    # php artisan test --filter=RoleControllerTest::test_store_deve_criar_perfil_com_sucesso
    public function test_store_deve_criar_perfil_com_sucesso(): void
    {
        $this->user->givePermissionTo(RolePermissionEnum::STORE->value);
        $permission1 = Permission::create(['name' => 'edit articles', 'guard_name' => 'api']);
        $permission2 = Permission::create(['name' => 'delete articles', 'guard_name' => 'api']);
        $roleData = [
            'name' => 'Editor',
            'permissions' => [$permission1->id, $permission2->id]
        ];

        $response = $this->withHeader('Authorization', "Bearer $this->token")
            ->postJson(route('roles.store'), $roleData);

        $response->assertStatus(201)
            ->assertJsonPath('data.name', 'Editor');

        $this->assertDatabaseHas('roles', ['name' => 'Editor', 'guard_name' => 'api']);

        // CORREÇÃO APLICADA AQUI
        $this->assertTrue(Role::findByName('Editor', 'api')->hasPermissionTo('edit articles'));
    }
}
