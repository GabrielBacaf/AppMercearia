<?php

namespace Tests\Feature\App\Htpp\Controllers\Api\V1;

use App\Enums\PaymentStatusEnum;
use App\Enums\PaymentTypeEnum;
use App\Enums\PurchasePermissionEnum;
use App\Enums\StatusEnum;
use App\Models\Payment;
use App\Models\Purchase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

# php artisan test --filter=PurchaseControllerTest
class PurchaseControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private string $token;

    private Purchase $purchase;

    private Payment $payment;



    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $this->purchase = Purchase::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $this->payment = Payment::factory()->create([
            'payable_id' => $this->purchase->id,
            'payable_type' => Purchase::class,
        ]);

        $this->token = $this->user->createToken('test-token')->plainTextToken;

        foreach (PurchasePermissionEnum::cases() as $permission) {
            Permission::create(['name' => $permission->value, 'guard_name' => 'api']);
        }
    }


    # php artisan test --filter=PurchaseControllerTest::test_deve_listar_compras_com_sucesso
    public function test_deve_listar_compras_com_sucesso(): void
    {

        //Arrange
        $this->user->givePermissionTo(PurchasePermissionEnum::INDEX->value);

        //Act
        $reponse = $this->withHeader('Authorization', "Bearer $this->token")
            ->getJson(route('purchases.index'));


        //Assert
        $reponse->assertStatus(200);
        $reponse->assertJsonStructure([
            'success',
            'message',
            'data' => [
                '*' => [
                    'id',
                    'title',
                    'description',
                    'supplier_id',
                    'invoice_id',
                    'user_id',
                    'purchase_date',
                    'count_value',
                    'status',
                    'payments',
                ]
            ],
            'meta' => [
                'total',
                'per_page',
                'current_page',
                'last_page',
            ]

        ]);
        $reponse->assertJsonCount(1, 'data');
    }

    # php artisan test --filter=PurchaseControllerTest::test_index_deve_retornar_erro_403_se_nao_autorizado
    public function test_index_deve_retornar_erro_403_se_nao_autorizado(): void
    {
        // Act
        $response = $this->withHeader('Authorization', "Bearer $this->token")
            ->getJson(route('purchases.index'));

        // Assert
        $response->assertStatus(403);
    }

    # php artisan test --filter=PurchaseControllerTest::test_show_deve_retornar_erro_403_se_nao_autorizado
    public function test_show_deve_retornar_erro_403_se_nao_autorizado(): void
    {
        // Act
        $response = $this->withHeader('Authorization', "Bearer $this->token")
            ->getJson(route('purchases.show', $this->purchase));

        // Assert
        $response->assertStatus(403);
    }

    # php artisan test --filter=PurchaseControllerTest::test_show_deve_retornar_erro_404_para_compra_inexistente
    public function test_show_deve_retornar_erro_404_para_compra_inexistente(): void
    {
        // Act
        $response = $this->withHeader('Authorization', "Bearer $this->token")
            ->getJson(route('purchases.show', 999));

        // Assert
        $response->assertStatus(404);
    }

    # php artisan test --filter=PurchaseControllerTest::test_show_deve_listar_compra_com_sucesso
    public function test_show_deve_listar_compra_com_sucesso(): void
    {
        //Arrange
        $this->user->givePermissionTo(PurchasePermissionEnum::SHOW->value);

        //Act
        $response = $this->withHeader('Authorization', "Bearer $this->token")
            ->getJson(route('purchases.show', $this->purchase));

        //Assert
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'title',
                'description',
                'supplier_id',
                'invoice_id',
                'user_id',
                'purchase_date',
                'count_value',
                'status',
                'payments',
            ]
        ]);
        $response->assertJsonPath('data.id', $this->purchase->id);
    }

    # php artisan test --filter=PurchaseControllerTest::test_store_deve_retornar_erro_403_se_nao_autorizado
    public function test_store_deve_retornar_erro_403_se_nao_autorizado(): void
    {
        //Arrange
        $dados = [
            'title' => 'Compra Teste',
            'description' => 'Descrição da compra teste',
            'user_id' => $this->user->id,
            'purchase_date' => 11 / 11 / 2023,
            'count_value' => 100,
            'status' => StatusEnum::PENDENTE->value,
        ];

        //Act
        $response = $this->withHeader('Authorization', "Bearer $this->token")
            ->postJson(route('purchases.store'), $dados);

        //Assert
        $response->assertStatus(403);
    }

    # php artisan test --filter=PurchaseControllerTest::test_store_deve_retornar_erro_de_validacao_com_dados_invalidos
    public function test_store_deve_retornar_erro_de_validacao_com_dados_invalidos(): void
    {
        //Arrange
        $this->user->givePermissionTo(PurchasePermissionEnum::STORE->value);
        $invalidData = [
            'title' => '',
            'description' => '',
            'user_id' => $this->user->id,
            'payments' => 'not-an-array',
            'purchase_date' => 'invalid-date'
        ];

        //Act
        $response = $this->withHeader('Authorization', "Bearer $this->token")
            ->postJson(route('purchases.store'), $invalidData);

        //Assert
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'description', 'user_id']);
    }


    # php artisan test --filter=PurchaseControllerTest::test_store_deve_criar_compra_com_sucesso
    public function test_store_deve_criar_compra_com_sucesso(): void
    {
        //Arrange
        $this->user->givePermissionTo(PurchasePermissionEnum::STORE->value);

        $dados = [
            'title' => 'Compra Teste',
            'purchase_date' => "2025-10-09",
            'payments' => [
                '*' => [
                    'value' => 100,
                    'payment_type' => PaymentTypeEnum::CREDITO->value,
                    'payment_status' => PaymentStatusEnum::DEVENDO->value,
                ],
            ]
        ];

        //Act
        $response = $this->withHeader('Authorization', "Bearer $this->token")
            ->postJson(route('purchases.store'),  $dados);

        //Assert
        $response->assertStatus(201)
            ->assertJsonPath('data.title', 'Compra Teste');

        $this->assertDatabaseHas('purchases', ['title' => 'Compra Teste']);
    }


    # php artisan test --filter=PurchaseControllerTest::test_update_deve_retornar_erro_403_se_nao_autorizado
    public function test_update_deve_retornar_erro_403_se_nao_autorizado(): void
    {
        //Act
        $reponse = $this->withHeader('Authorization', "Bearer $this->token")
            ->putJson(route('purchases.update', $this->purchase), $dados = []);

        //Assert

        $reponse->assertStatus(403);
    }

    # php artisan test --filter=PurchaseControllerTest::test_update_deve_retornar_erro_404_para_compra_inexistente
    public function test_update_deve_retornar_erro_404_para_compra_inexistente(): void
    {
        //Arrange
        $this->user->givePermissionTo(PurchasePermissionEnum::UPDATE->value);

        //Act
        $reponse = $this->withHeader('Authorization', "Bearer $this->token")
            ->putJson(route('purchases.update', 99), $dados = []);

        //Assert
        $reponse->assertStatus(404);
    }

    # php artisan test --filter=PurchaseControllerTest::test_update_deve_atualizar_compra_com_sucesso
    public function test_update_deve_atualizar_compra_com_sucesso(): void
    {
        //Arrange
        $this->user->givePermissionTo(PurchasePermissionEnum::UPDATE->value);

        $dados = [
            'title' => 'Compra Teste',
            'description' => 'Descrição da compra teste',
            'purchase_date' => "2025-10-09",
            'payments' => [
                '*' => [
                    'value' => 100,
                    'payment_type' => PaymentTypeEnum::DINHEIRO->value,
                    'payment_status' => PaymentStatusEnum::PAGO->value,
                ],
            ]
        ];

        //Act

        $reponse = $this->withHeader('Authorization', "Bearer $this->token")
            ->putJson(route('purchases.update', $this->purchase), $dados);

        //Assert
        $reponse->assertStatus(200)
            ->assertJsonPath('data.title', 'Compra Teste')
            ->assertJsonPath('data.description', 'Descrição da compra teste');
    }
}
