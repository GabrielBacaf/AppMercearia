<?php

namespace Tests\Feature\App\Htpp\Controllers\Api\V1;

use App\Enums\UserPermissionEnum;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

# php artisan test --filter=UserControllerTest
class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    private $user;
    private $token;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->token = $this->user->createToken('Pc-Baca')->plainTextToken;
    }

    # php artisan test --filter=UserControllerTest::test_criando_usuario_com_sucesso
    public function test_criando_usuario_com_sucesso(): void
    {
        //Arrange
        Permission::create([
            'name' => UserPermissionEnum::CREATE->value,
            'guard_name' => 'api',
        ]);

        $dados = [
            'name' => 'Usuário Teste',
            'login' => 'teste_login',
            'email' => 'teste@example.com',
            'password' => '123456789',
            'password_confirmation' => '123456789'
        ];

        $this->user->givePermissionTo(UserPermissionEnum::CREATE->value);

        //Act
        $response = $this->withHeader('Authorization', "Bearer $this->token")
            ->postJson(route('users.store'), $dados);

        //Assert
        $response->assertStatus(201);
        $response->assertJson([
            'success' => true,
            'message' => 'Usuário criado com sucesso!',
        ]);

        $this->assertDatabaseHas('users', [
            'name' => $dados['name'],
            'login' => $dados['login'],
            'email' => $dados['email'],
        ]);
    }

    # php artisan test --filter=UserControllerTest::test_atualizando_usuario_com_sucesso
    public function test_atualizando_usuario_com_sucesso()
    {
        //Arrange
        Permission::create([
            'name' => UserPermissionEnum::UPDATE->value,
            'guard_name' => 'api',
        ]);

        $dados = [
            'name' => 'GabrielFerreira',
            'login' => 'bonitao',
        ];

        $this->user->givePermissionTo(UserPermissionEnum::UPDATE->value);

        //Act
        $response = $this->withHeader('Authorization', "Bearer $this->token")
            ->putJson(route('users.update', $this->user), $dados);

        //Assert
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Usuário atualizado com sucesso!',
        ]);

        $this->assertDatabaseHas('users', [
            'name' => $dados['name'],
            'login' => $dados['login'],
        ]);
    }

    # php artisan test --filter=UserControllerTest::test_deletar_usuario_com_sucesso
    public function test_deletar_usuario_com_sucesso(): void
    {
        //Arrange
        Permission::create([
            'name' => UserPermissionEnum::DESTROY->value,
            'guard_name' => 'api',
        ]);

        $this->user->givePermissionTo(UserPermissionEnum::DESTROY->value);

        $response = $this->withHeader('Authorization', "Bearer $this->token")
            ->deleteJson(route('users.destroy', $this->user));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Usuário deletado com sucesso!',
        ]);

        $this->assertDatabaseMissing('users', [
            'name' => $this->user->name,
            'login' => $this->user->email
        ]);
    }

    # php artisan test --filter=UserControllerTest::test_listar_todos_usuarios_com_sucesso
    public function test_listar_todos_usuarios_com_sucesso(): void
    {
        //Arrange
        Permission::create([
            'name' => UserPermissionEnum::INDEX->value,
            'guard_name' => 'api',
        ]);

        $this->user->givePermissionTo(UserPermissionEnum::INDEX->value);

        //Act
        $response = $this->withHeader('Authorization', "Bearer $this->token")
            ->getJson(route('users.index'));

        //Assert
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Usuário listados com sucesso!',
        ]);

        $this->assertNotEmpty($response->json('data'));

        $response->assertJsonFragment([
            'id' => $this->user->id,
            'name' => $this->user->name,
            'login' => $this->user->login,
            'email' => $this->user->email,
        ]);
    }

    # php artisan test --filter=UserControllerTest::test_vizualizar_um_usuario_especifico_com_sucesso
    public function test_vizualizar_um_usuario_especifico_com_sucesso(): void
    {
        //Arrange
        Permission::create([
            'name' => UserPermissionEnum::SHOW->value,
            'guard_name' => 'api',
        ]);

        $this->user->givePermissionTo(UserPermissionEnum::SHOW->value);

        //Act
        $response = $this->withHeader('Authorization', "Bearer $this->token")
            ->getJson(route('users.show', $this->user));

        //Assert
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Usuário detalhado com sucesso!',
        ]);

        $this->assertNotEmpty($response->json('data'));

        $response->assertJsonFragment([
            'id' => $this->user->id,
            'name' => $this->user->name,
            'login' => $this->user->login,
            'email' => $this->user->email,
        ]);
    }
}
