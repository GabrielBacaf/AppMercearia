<?php

namespace Tests\Feature\App\Htpp\Controllers\Api\V1;

use App\Enums\UserPermissionEnum;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

# php artisan test --filter=UserControllerTest
class UserControllerTest extends TestCase
{
    private Role $role;
    public function setUp(): void
    {
        parent::setUp();

        $this->role = Role::create(['name' => 'Test Role', 'guard_name' => 'api']);
    }

    # php artisan test --filter=UserControllerTest::test_listar_todos_usuarios_com_sucesso
    public function test_listar_todos_usuarios_com_sucesso(): void
    {
        //Arrange
        $this->user->givePermissionTo(UserPermissionEnum::INDEX->value);

        //Act
        $response = $this->withHeader('Authorization', "Bearer $this->token")
            ->getJson(route('users.index'));

        //Assert
        $response->assertStatus(200);
        $response->assertJsonFragment(['name' => $this->user->name]);
    }

    # php artisan test --filter=UserControllerTest::test_index_deve_retornar_erro_403_se_nao_autorizado
    public function test_index_deve_retornar_erro_403_se_nao_autorizado(): void
    {
        // Act
        $response = $this->withHeader('Authorization', "Bearer $this->token")
            ->getJson(route('users.index'));

        //Assert
        $response->assertStatus(403);
    }

    # php artisan test --filter=UserControllerTest::test_visualizar_um_usuario_especifico_com_sucesso
    public function test_visualizar_um_usuario_especifico_com_sucesso(): void
    {
        //Arrange
        $this->user->givePermissionTo(UserPermissionEnum::SHOW->value);

        //Act
        $response = $this->withHeader('Authorization', "Bearer $this->token")
            ->getJson(route('users.show', $this->user));

        //Assert
        $response->assertStatus(200);
        $response->assertJsonFragment(['id' => $this->user->id]);
    }

    # php artisan test --filter=UserControllerTest::test_show_deve_retornar_erro_403_se_nao_autorizado
    public function test_show_deve_retornar_erro_403_se_nao_autorizado(): void
    {
        //Arrange
        $anotherUser = User::factory()->create();

        //Act
        $response = $this->withHeader('Authorization', "Bearer $this->token")
            ->getJson(route('users.show', $anotherUser));

        //Assert
        $response->assertStatus(403);
    }

    # php artisan test --filter=UserControllerTest::test_show_deve_retornar_erro_404_para_usuario_inexistente
    public function test_show_deve_retornar_erro_404_para_usuario_inexistente(): void
    {
        //Arrange
        $this->user->givePermissionTo(UserPermissionEnum::SHOW->value);
        $nonExistentUserId = 999;

        //Act
        $response = $this->withHeader('Authorization', "Bearer $this->token")
            ->getJson(route('users.show', $nonExistentUserId));

        //Assert
        $response->assertStatus(404);
    }


    # php artisan test --filter=UserControllerTest::test_criando_usuario_com_sucesso
    public function test_criando_usuario_com_sucesso(): void
    {
        //Arrange
        $this->user->givePermissionTo(UserPermissionEnum::STORE->value);

        $dados = [
            'name' => 'Usuário Teste',
            'login' => 'teste_login',
            'email' => 'teste@example.com',
            'password' => '123456789',
            'password_confirmation' => '123456789',
            'roles' => [$this->role->name],
        ];

        //Act
        $response = $this->withHeader('Authorization', "Bearer $this->token")
            ->postJson(route('users.store'), $dados);

        //Assert
        $response->assertStatus(201);
        $this->assertDatabaseHas('users', ['login' => 'teste_login']);
    }

    # php artisan test --filter=UserControllerTest::test_store_deve_retornar_erro_403_se_nao_autorizado
    public function test_store_deve_retornar_erro_403_se_nao_autorizado(): void
    {
        //Arrange
        $dados = [
            'name' => 'Usuário Teste',
            'login' => 'teste_login_403',
            'email' => 'teste403@example.com',
            'password' => '123456789',
            'password_confirmation' => '123456789',
            'roles' => [$this->role->name],
        ];

        //Act
        $response = $this->withHeader('Authorization', "Bearer $this->token")
            ->postJson(route('users.store'), $dados);

        //Assert
        $response->assertStatus(403);
    }

    # php artisan test --filter=UserControllerTest::test_store_deve_retornar_erro_422_com_dados_invalidos
    public function test_store_deve_retornar_erro_422_com_dados_invalidos(): void
    {
        //Arrange
        $this->user->givePermissionTo(UserPermissionEnum::STORE->value);

        $dadosInvalidos = [
            'name' => '', // Nome em branco
            'login' => $this->user->login, // Login já existente
            'password' => '123', // Senha curta
            'roles' => 'nao-e-um-array' // Roles inválido
        ];

        //Act
        $response = $this->withHeader('Authorization', "Bearer $this->token")
            ->postJson(route('users.store'), $dadosInvalidos);

        //Assert
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'login', 'password', 'roles']);
    }

    # php artisan test --filter=UserControllerTest::test_atualizando_usuario_com_sucesso
    public function test_atualizando_usuario_com_sucesso(): void
    {
        //Arrange
        $this->user->givePermissionTo(UserPermissionEnum::UPDATE->value);

        $dados = [
            'name' => 'GabrielFerreira',
            'login' => 'bonitao',
            'roles' => [$this->role->name],
        ];

        //Act
        $response = $this->withHeader('Authorization', "Bearer $this->token")
            ->putJson(route('users.update', $this->user), $dados);

        //Assert
        $response->assertStatus(200);
        $this->assertDatabaseHas('users', ['login' => 'bonitao']);
    }

    # php artisan test --filter=UserControllerTest::test_update_deve_retornar_erro_403_se_nao_autorizado
    public function test_update_deve_retornar_erro_403_se_nao_autorizado(): void
    {
        //Arrange
        $dados = [
            'name' => 'Nome Novo',
            'login' => 'login_novo_403',
            'roles' => [$this->role->name],
        ];

        $anotherUser = User::factory()->create();

        //Assert
        $response = $this->withHeader('Authorization', "Bearer $this->token")
            ->putJson(route('users.update', $anotherUser), $dados);

        $response->assertStatus(403);
    }

    # php artisan test --filter=UserControllerTest::test_deletar_usuario_com_sucesso
    public function test_deletar_usuario_com_sucesso(): void
    {
        //Arrange
        $this->user->givePermissionTo(UserPermissionEnum::DESTROY->value);
        $userToDelete = User::factory()->create();

        //Act
        $response = $this->withHeader('Authorization', "Bearer $this->token")
            ->deleteJson(route('users.destroy', $userToDelete));

        //Assert
        $response->assertStatus(200);
        $this->assertDatabaseMissing('users', ['id' => $userToDelete->id]);
    }

    # php artisan test --filter=UserControllerTest::test_destroy_deve_retornar_erro_403_se_nao_autorizado
    public function test_destroy_deve_retornar_erro_403_se_nao_autorizado(): void
    {
        //Arrange
        $userToDelete = User::factory()->create();

        //Act
        $response = $this->withHeader('Authorization', "Bearer $this->token")
            ->deleteJson(route('users.destroy', $userToDelete));

        //Assert
        $response->assertStatus(403);
    }

    # php artisan test --filter=UserControllerTest::test_destroy_deve_retornar_erro_404_para_usuario_inexistente
    public function test_destroy_deve_retornar_erro_404_para_usuario_inexistente(): void
    {
        //Arrange
        $this->user->givePermissionTo(UserPermissionEnum::DESTROY->value);
        $nonExistentUserId = 999;

        //Act
        $response = $this->withHeader('Authorization', "Bearer $this->token")
            ->deleteJson(route('users.destroy', $nonExistentUserId));

        //Assert
        $response->assertStatus(404);
    }
}
