<?php

namespace Tests\Feature\App\Htpp\Controllers\Api\V1;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

 # php artisan test --filter=AuthControllerTest
class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    private $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    # php artisan test --filter=AuthControllerTest::test_logando_com_sucesso
    public function test_logando_com_sucesso()
    {
        //Act
        $response = $this->postJson(route('login'), [
            'login' => $this->user->login,
            'password' => '12345678',
            'device_name' => 'testing'
        ]);

        //Assert
        $response->assertStatus(200);

        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'access_token',
                'token_type',
            ]
        ]);

        $response->assertJson([
            'success' => true,
            'message' => 'Seja bem-vindo(a)!',
        ]);

        $this->assertNotEmpty($response['data']['access_token']);
    }

    # php artisan test --filter=AuthControllerTest::test_deslogando_com_sucesso
    public function test_deslogando_com_sucesso(): void
    {

        //Arrange
        $token = $this->user->createToken('testing')->plainTextToken;
        

        //Act
        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson(route('logout'));

        //Assert
        $response->assertStatus(200);

        $response->assertJson([
            'success' => true,
            'message' => 'Token Revoked',
            'data' => [],
        ]);

        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_id' => $this->user->id,
        ]);
    }
}
