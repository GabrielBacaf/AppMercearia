<?php

namespace Tests\Feature\App\Htpp\Controllers\Api\V1;

use App\Enums\PermissionEnum;
use App\Models\User;
use Database\Factories\PermissionFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

# php artisan test --filter=PermissionControllerTest
class PermissionControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private string $token;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->token = $this->user->createToken('test-token')->plainTextToken;
        Permission::create(['name' => PermissionEnum::INDEX->value, 'guard_name' => 'api']);
    }

    # php artisan test --filter=PermissionControllerTest::test_deve_retornar_erro_403_se_usuario_nao_autorizado
    public function test_deve_retornar_erro_403_se_usuario_nao_autorizado(): void
    {
        // Act
        $response = $this->withHeader('Authorization', "Bearer $this->token")
            ->getJson(route('permissions.index'));

        // Assert
        $response->assertStatus(403);
    }

    # php artisan test --filter=PermissionControllerTest::test_deve_listar_permissoes_com_sucesso
    public function test_deve_listar_permissoes_com_sucesso(): void
    {
        // Arrange
        $this->user->givePermissionTo(PermissionEnum::INDEX->value);
        PermissionFactory::new()->count(3)->create(['guard_name' => 'api']);

        // Act
        $response = $this->withHeader('Authorization', "Bearer $this->token")
            ->getJson(route('permissions.index'));

        // Assert
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                '*' => ['id', 'name']
            ]
        ]);

        $response->assertJsonCount(4, 'data');
    }
}
