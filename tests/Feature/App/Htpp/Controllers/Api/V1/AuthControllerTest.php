<?php

namespace Tests\Feature\App\Htpp\Controllers\Api\V1;
use Illuminate\Foundation\Testing\RefreshDatabase;

use Tests\TestCase;

 # php artisan test --filter=AuthControllerTest
class AuthControllerTest extends TestCase
{
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
        //Act
        $response = $this->withHeader('Authorization', "Bearer $this->token")
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
