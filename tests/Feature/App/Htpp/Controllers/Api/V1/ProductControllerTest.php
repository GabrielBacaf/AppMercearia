<?php

namespace Tests\Feature\App\Htpp\Controllers\Api\V1;

use App\Enums\ProductPermissionEnum;
use App\Enums\PurchasePermissionEnum;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

# php artisan test --filter=ProductControllerTest
class ProductControllerTest extends TestCase
{
    private Purchase $purchase;

    private Payment $payment;


    private Product $product;

    private array $productMake;

    public function setUp(): void
    {
        parent::setUp();

        $this->purchase = Purchase::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $this->productMake =  Product::factory()->make([
            'purchase_id' => $this->purchase->id,
            'expiration_date' => '2599-10-09',
            'amount' => 100,
            'purchase_value' => 50,
            'stock_quantity' => '',

        ])->toArray();

        $this->payment = Payment::factory()->create([
            'payable_id' => $this->purchase->id,
            'payable_type' => Purchase::class,
        ]);

        $this->product = Product::factory()->create();

    }

    #php artisan test --filter=ProductControllerTest::test_deve_listar_produtos
    public function test_deve_listar_produtos_com_sucesso(): void
    {
        //Arrange
        $this->user->givePermissionTo(ProductPermissionEnum::INDEX->value);

        //Act
        $reponse = $this->withHeader('Authorization', "Bearer $this->token")
            ->getJson(route('products.index'));

        //Assert
        $reponse->assertStatus(200);
        $reponse->assertJsonStructure([
            'success',
            'message',
            'data' => [
                '*' => [
                    'barcode',
                    'name',
                    'expiration_date',
                    'category',
                    'sale_value',
                    'stock_quantity',
                ],
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

    #php artisan test --filter=ProductControllerTest::test_index_deve_retornar_erro_403_se_nao_autorizado
    public function test_index_deve_retornar_erro_403_se_nao_autorizado(): void
    {
        // Act
        $reponse = $this->withHeader('Authorization', "Bearer $this->token")
            ->getJson(route('products.index'));

        // Assert
        $reponse->assertStatus(403);
    }

    #php artisan test --filter=ProductControllerTest::test_show_deve_retornar_erro_403_se_nao_autorizado
    public function test_show_deve_retornar_erro_403_se_nao_autorizado(): void
    {
        // Act
        $reponse = $this->withHeader('Authorization', "Bearer $this->token")
            ->getJson(route('products.show', $this->product));

        // Assert
        $reponse->assertStatus(403);
    }

    # php artisan test --filter=ProductControllerTest::test_show_deve_retornar_erro_404_para_produto_inexistente
    public function test_show_deve_retornar_erro_404_para_produto_inexistente(): void
    {
        //Arrange
        $this->user->givePermissionTo(ProductPermissionEnum::SHOW->value);

        // Act
        $reponse = $this->withHeader('Authorization', "Bearer $this->token")
            ->getJson(route('products.show', 999999));

        //Assert
        $reponse->assertStatus(404);
    }

    # php artisan test --filter=ProductControllerTest::test_show_deve_listar_produto_com_sucesso
    public function test_show_deve_listar_produto_com_sucesso(): void
    {
        //Arrange
        $this->user->givePermissionTo(ProductPermissionEnum::SHOW->value);

        //Act
        $reponse = $this->withHeader('Authorization', "Bearer $this->token")
            ->getJson(route('products.show', $this->product));

        //Assert
        $reponse->assertStatus(200);

        $reponse->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'barcode',
                'name',
                'expiration_date',
                'sale_value',
                'category',
                'stock_quantity',
            ]
        ]);
        $reponse->assertJsonPath('data.id', $this->product->id);
        $reponse->assertJsonPath('data.barcode', $this->product->barcode);
        $reponse->assertJsonPath('data.name', $this->product->name);
    }

    # php artisan test --filter=ProductControllerTest::test_store_deve_retornar_erro_403_se_nao_autorizado
    public function test_store_deve_retornar_erro_403_se_nao_autorizado(): void
    {
        //Arrange
        $data = Product::factory()->make([
            'purchase_id' => $this->purchase->id,
        ])->toArray();

        //Act
        $reponse = $this->withHeader('Authorization', "Bearer $this->token")
            ->postJson(route('products.store'), $data);

        //Assert
        $reponse->assertStatus(403);
    }

    # php artisan test --filter=ProductControllerTest::test_store_deve_retornar_erro_de_validacao_com_dados_invalidos
    public function test_store_deve_retornar_erro_de_validacao_com_dados_invalidos(): void
    {
        //Arrange
        $this->user->givePermissionTo(ProductPermissionEnum::STORE->value);

        $invalidData = [
            'name' => '',
            'barcode' => '',
            'expiration_date' => 'DSDS',
            'category' => 'DSS',
            'sale_value' => 'DSDS',
            'stock_quantity' => 'SD',
        ];

        //Act

        $reponse = $this->withHeader('Authorization', "Bearer $this->token")
            ->postJson(route('products.store'), $invalidData);

        //Assert
        $reponse->assertStatus(422);
    }

    # php artisan test --filter=ProductControllerTest::test_store_deve_criar_produto_com_sucesso
    public function test_store_deve_criar_produto_com_sucesso(): void
    {
        //Arrange
        $this->user->givePermissionTo(ProductPermissionEnum::STORE->value);

        //Act
        $reponse = $this->withHeader('Authorization', "Bearer $this->token")
            ->postJson(route('products.store'), $this->productMake);

        //Assert
        $reponse->assertStatus(201);
        $reponse->assertJsonPath('data.name', $this->productMake['name']);
    }

    # php artisan test --filter=ProductControllerTest::test_update_deve_retornar_erro_403_se_nao_autorizado
    public function test_update_deve_retornar_erro_403_se_nao_autorizado(): void
    {
        //Act
        $reponse = $this->withHeader('Authorization', "Bearer $this->token")
            ->putJson(route('products.update', $this->product), $this->productMake);

        //Assert
        $reponse->assertStatus(403);
    }

    # php artisan test --filter=ProductControllerTest::test_update_deve_retornar_erro_404_para_produto_inexistente
    public function test_update_deve_retornar_erro_404_para_produto_inexistente(): void
    {
        //Arrange
        $this->user->givePermissionTo(ProductPermissionEnum::UPDATE->value);

        //Act
        $reponse = $this->withHeader('Authorization', "Bearer $this->token")
            ->putJson(route('products.update', 999999999999999999), $this->productMake);

        //Assert
        $reponse->assertStatus(404);
    }

    # php artisan test --filter=ProductControllerTest::test_update_deve_atualizar_produto_com_sucesso
    public function test_update_deve_atualizar_produto_com_sucesso(): void
    {
        //
        $this->user->givePermissionTo(ProductPermissionEnum::UPDATE->value);
         $this->productMake['expiration_date'] = '2199-10-09 ';


        //Act
        $reponse = $this->withHeader('Authorization', "Bearer $this->token")
            ->putJson(route('products.update', $this->product), $this->productMake);

        //Assert
        $reponse->assertStatus(200);
    }
}
