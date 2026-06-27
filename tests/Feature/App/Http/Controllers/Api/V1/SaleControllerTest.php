<?php

namespace Tests\Feature\App\Http\Controllers\Api\V1;

use App\Enums\PaymentStatusEnum;
use App\Enums\PaymentTypeEnum;
use App\Enums\SalePermissionEnum;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

# php artisan test --filter=SaleControllerTest
class SaleControllerTest extends TestCase
{
    use RefreshDatabase;

    private Product $product1;
    private Product $product2;

    public function setUp(): void
    {
        parent::setUp(); // O seu parent já cria o $this->user e o $this->token

        // Forçamos o preço e o estoque aqui para o teste matemático funcionar perfeitamente
        $this->product1 = Product::factory()->create([
            'sale_value' => 15.00,
            'stock_quantity' => 10
        ]);

        $this->product2 = Product::factory()->create([
            'sale_value' => 20.00,
            'stock_quantity' => 5
        ]);
    }

    # php artisan test --filter=SaleControllerTest::test_store_deve_retornar_erro_403_se_nao_autorizado
    public function test_store_deve_retornar_erro_403_se_nao_autorizado(): void
    {
        $payload = [
            'products' => [['id' => $this->product1->id, 'quantity' => 1]],
            'payments' => [['payment_type' => 'PIX', 'payment_status' => 'PAGO', 'value' => 15.00]]
        ];

        // Act
        $response = $this->withHeader('Authorization', "Bearer $this->token")
            ->postJson(route('sales.store'), $payload);

        // Assert
        $response->assertStatus(403);
    }

    # php artisan test --filter=SaleControllerTest::test_store_deve_criar_venda_com_sucesso_e_calcular_totais
    public function test_store_deve_criar_venda_com_sucesso_e_calcular_totais(): void
    {
        // Arrange
        $this->user->givePermissionTo(SalePermissionEnum::STORE->value);

        // Produto 1: 2 un x 15.00 = 30.00
        // Produto 2: 1 un x 20.00 = 20.00
        // Subtotal = 50.00 | Frete = + 5.00 | Desconto = - 2.00 | TOTAL ESPERADO: 53.00
        $payload = [
            'discount' => 2.00,
            'delivery_price' => 5.00,
            'products' => [
                ['id' => $this->product1->id, 'quantity' => 2],
                ['id' => $this->product2->id, 'quantity' => 1],
            ],
            'payments' => [
                [
                    'payment_type' => PaymentTypeEnum::PIXEMPRESA->value,
                    'payment_status' => PaymentStatusEnum::PAGO->value,
                    'value' => 53.00
                ]
            ]
        ];

        // Act
        $response = $this->withHeader('Authorization', "Bearer $this->token")
            ->postJson(route('sales.store'), $payload);

        // Assert
        $response->assertStatus(201);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'discount',
                'delivery_price',
                'total_value',
                // Aqui você pode adicionar as outras chaves que seu SaleResource retorna
            ]
        ]);

        // Verifica a integridade dos dados na base
        $this->assertDatabaseHas('sales', [
            'total_value' => 53.00,
            'user_id' => $this->user->id,
        ]);

        $this->assertDatabaseHas('product_sale', [
            'product_id' => $this->product1->id,
            'amount' => 2,
            'sale_value' => 15.00
        ]);

        $this->assertDatabaseHas('products', [
            'id' => $this->product1->id,
            'stock_quantity' => 8 // Baixou 2 de 10
        ]);

        $this->assertDatabaseHas('payments', [
            'payable_type' => \App\Models\Sale::class,
            'value' => 53.00,
        ]);
    }

    # php artisan test --filter=SaleControllerTest::test_store_deve_falhar_se_estoque_for_insuficiente
    public function test_store_deve_falhar_se_estoque_for_insuficiente(): void
    {
        // Arrange
        $this->user->givePermissionTo(SalePermissionEnum::STORE->value);

        $payload = [
            'products' => [
                ['id' => $this->product2->id, 'quantity' => 10], // Tem apenas 5 no estoque
            ],
            'payments' => [
                [
                    'payment_type' => PaymentTypeEnum::PIXEMPRESA->value,
                    'payment_status' => PaymentStatusEnum::PAGO->value,
                    'value' => 200.00
                ]
            ]
        ];

        // Act
        $response = $this->withHeader('Authorization', "Bearer $this->token")
            ->postJson(route('sales.store'), $payload);

        // Assert
        $response->assertStatus(400); // 400 Bad Request retornado pelo Catch do Controller
        $response->assertJsonPath('message', "Estoque insuficiente para o produto: {$this->product2->name}");

        // Verifica se NENHUMA venda foi criada (Rollback funcionou)
        $this->assertDatabaseCount('sales', 0);
    }
}
