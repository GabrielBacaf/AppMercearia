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

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Purchase $purchase;

    private Payment $payment;


    private Product $product;


    private string $token;



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

        $this->product = Product::factory()->create();


        // foreach (ProductPermissionEnum::cases() as $permission) {
        //     Permission::create(['name' => $permission->value, 'guard_name' => 'api']);
        // }

        $this->token = $this->user->createToken('test-token')->plainTextToken;
    }

    #php artisan test --filter=ProductControllerTest::test_deve_listar_produtos
    public function test_deve_listar_produtos(): void
    {
        //Arrange
        $this->user->givePermissionTo(ProductPermissionEnum::INDEX->value);

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
}
