<?php

namespace Tests\Feature\App\Htpp\Controllers\Api\V1;

use App\Enums\ClientPermissionEnum;
use App\Models\Client;
use Tests\TestCase;

# php artisan test --filter=ClienteControllerTest
class ClienteControllerTest extends TestCase
{
    private Client $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = Client::factory()->create();
    }

    # php artisan test --filter=ClienteControllerTest::test_deve_listar_clientes_com_sucesso
    public function test_deve_listar_clientes_com_sucesso(): void
    {
        //Arrange
        $this->user->givePermissionTo(ClientPermissionEnum::INDEX->value);

        //Act
        $response = $this->withHeader('Authorization', "Bearer $this->token")
            ->getJson(route('clients.index'));

        //Assert
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                '*' => ['id', 'name', 'email', 'phone']
            ]
        ]);

        $response->assertJsonCount(1, 'data');
    }

    # php artisan test --filter=ClienteControllerTest::test_index_deve_retornar_erro_403_se_nao_autorizado
    public function test_index_deve_retornar_erro_403_se_nao_autorizado(): void
    {
        // Act
        $response = $this->withHeader('Authorization', "Bearer $this->token")
            ->getJson(route('clients.index'));

        // Assert
        $response->assertStatus(403);
    }

    # php artisan test --filter=ClienteControllerTest::test_show_deve_retornar_erro_403_se_nao_autorizado
    public function test_show_deve_retornar_erro_403_se_nao_autorizado(): void
    {
        // Act
        $response = $this->withHeader('Authorization', "Bearer $this->token")
            ->getJson(route('clients.show', $this->client));

        // Assert
        $response->assertStatus(403);
    }

    # php artisan test --filter=ClienteControllerTest::test_show_deve_retornar_erro_404_para_cliente_inexistente
    public function test_show_deve_retornar_erro_404_para_cliente_inexistente(): void
    {
        // Act
        $response = $this->withHeader('Authorization', "Bearer $this->token")
            ->getJson(route('clients.show', 9999999999999));

        // Assert
        $response->assertStatus(404);
    }

    # php artisan test --filter=ClienteControllerTest::test_show_deve_listar_cliente_com_sucesso
    public function test_show_deve_listar_cliente_com_sucesso(): void
    {
        //Arrange
        $this->user->givePermissionTo(ClientPermissionEnum::SHOW->value);

        //Act
        $response = $this->withHeader('Authorization', "Bearer $this->token")
            ->getJson(route('clients.show', $this->client));

        //Assert
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'name',
                'email',
                'phone',
                'address' => [
                    'street',
                    'number',
                    'complement',
                    'city',
                    'state',
                    'postal_code',
                    'country',
                    'latitude',
                    'longitude',
                ],
            ]
        ]);
        $response->assertJsonPath('data.id', $this->client->id);
    }

    # php artisan test --filter=ClienteControllerTest::test_store_deve_retornar_erro_403_se_nao_autorizado
    public function test_store_deve_retornar_erro_403_se_nao_autorizado(): void
    {
        //Arrange
        $data = Client::factory()->make()->toArray();

        // Act
        $response = $this->withHeader('Authorization', "Bearer $this->token")
            ->postJson(route('clients.store'), $data);

        // Assert
        $response->assertStatus(403);
    }

    # php artisan test --filter=ClienteControllerTest::test_store_deve_retornar_erro_422_com_dados_invalidos
    public function test_store_deve_retornar_erro_422_com_dados_invalidos(): void
    {
        //Arrange
        $this->user->givePermissionTo(ClientPermissionEnum::STORE->value);

        $invalidData = [
            'name' => '',
            'email' => '',
            'phone' => '',
        ];

        // Act
        $response = $this->withHeader('Authorization', "Bearer $this->token")
            ->postJson(route('clients.store'), $invalidData);

        // Assert
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name', 'email', 'phone']);
    }

    #php artisan test --filter=ClienteControllerTest::test_store_deve_criar_cliente_com_endereco_com_sucesso
    public function test_store_deve_criar_cliente_com_endereco_com_sucesso(): void
    {
        //Arrange
        $this->user->givePermissionTo(ClientPermissionEnum::STORE->value);
        $data = Client::factory()->make([
            'address' => [
                'street' => 'Rua Teste',
                'number' => '123',
                'city' => 'Cidade Teste',
                'state' => 'Estado Teste',
                'postal_code' => '12345-678',
                'country' => 'País Teste',
            ]
        ])->toArray();

        // Act
        $response = $this->withHeader('Authorization', "Bearer $this->token")
            ->postJson(route('clients.store'), $data);

        // Assert
        $response->assertStatus(201);
        $response->assertJsonPath('data.name', $data['name']);

    }

    # php artisan test --filter=ClienteControllerTest::test_update_deve_retornar_erro_403_se_nao_autorizado
    public function test_update_deve_retornar_erro_403_se_nao_autorizado(): void
    {
        //Arrange
        $data = Client::factory()->make()->toArray();

        // Act
        $response = $this->withHeader('Authorization', "Bearer $this->token")
            ->putJson(route('clients.update', $this->client), $data);

        // Assert
        $response->assertStatus(403);
    }

    # php artisan test --filter=ClienteControllerTest::test_update_deve_retornar_erro_404_para_cliente_inexistente
    public function test_update_deve_retornar_erro_404_para_cliente_inexistente(): void
    {
        //Arrange
        $this->user->givePermissionTo(ClientPermissionEnum::UPDATE->value);
        $data = Client::factory()->make()->toArray();

        // Act
        $response = $this->withHeader('Authorization', "Bearer $this->token")
            ->putJson(route('clients.update', 999999), $data);

        // Assert
        $response->assertStatus(404);
    }

    # php artisan test --filter=ClienteControllerTest::test_update_deve_atualizar_cliente_com_sucesso
    public function test_update_deve_atualizar_cliente_com_sucesso(): void
    {
        //Arrange
        $this->user->givePermissionTo(ClientPermissionEnum::UPDATE->value);
        $data = Client::factory()->make([
            'address' => [
                'street' => 'Teste',
                'number' => '123456789',
                'city' => 'Cidade VG',
                'state' => 'Estado MT',
                'postal_code' => '123345-678',
                'country' => 'País Teste',
            ]
        ])->toArray();

        // Act
        $response = $this->withHeader('Authorization', "Bearer $this->token")
            ->putJson(route('clients.update', $this->client), $data);

        // Assert
        $response->assertStatus(200);
        $response->assertJsonPath('data.name', $data['name']);
    }

}
