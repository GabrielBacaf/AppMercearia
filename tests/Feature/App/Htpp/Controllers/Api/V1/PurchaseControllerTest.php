<?php

namespace Tests\Feature\App\Htpp\Controllers\Api\V1;

use App\Enums\PurchasePermissionEnum;
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

        Permission::create(['name' => PurchasePermissionEnum::INDEX->value, 'guard_name' => 'api']);
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
                    'purchase_date',
                    'payments',

                ]
            ]
        ]);
        $reponse->assertJsonCount(1, 'data');

       $reponse->assertJsonIsObject('data', [])





    }
}
