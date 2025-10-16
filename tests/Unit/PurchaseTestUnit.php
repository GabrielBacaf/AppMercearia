<?php

namespace Tests\Unit;

use App\Enums\PaymentStatusEnum;
use App\Enums\PurchasePermissionEnum;
use App\Enums\StatusEnum;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

# php artisan test --filter=PurchaseTest
class PurchaseTestUnit extends TestCase
{
    use RefreshDatabase;

    private Purchase $purchase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $this->purchase = Purchase::factory()->create([
            'user_id' => $this->user->id,
        ]);
    }

    # php artisan test --filter=PurchaseTest::test_update_status_deve_definir_status_como_finalizado
    public function test_update_status_deve_definir_status_como_finalizado(): void
    {
        // Arrange
        $product = Product::factory()->create([]);
        $this->purchase->products()->attach($product->id, [
            'amount' => 2,
            'purchase_value' => 50
        ]);

        Payment::factory()->create([
            'payable_id' => $this->purchase->id,
            'payable_type' => Purchase::class,
            'value' => 100,
            'payment_status' => PaymentStatusEnum::PAGO->value,
        ]);

        // Act
        $this->purchase->updateStatus();

        // Assert
        $this->assertEquals(0, $this->purchase->count_value);
        $this->assertEquals(StatusEnum::FINALIZADO->value, $this->purchase->status);
    }

    # php artisan test --filter=PurchaseTest::test_update_status_deve_definir_status_como_pagamento_pendente
    public function test_update_status_deve_definir_status_como_pagamento_pendente(): void
    {
        // Arrange
        $product = Product::factory()->create();
        $this->purchase->products()->attach($product->id, ['purchase_value' => 200, 'amount' => 5]);

        Payment::factory()->create([
            'payable_id' => $this->purchase->id,
            'payable_type' => Purchase::class,
            'value' => 500,
            'payment_status' => PaymentStatusEnum::PAGO->value,
        ]);
        Payment::factory()->create([
            'payable_id' => $this->purchase->id,
            'payable_type' => Purchase::class,
            'value' => 500,
            'payment_status' => PaymentStatusEnum::DEVENDO->value,
        ]);

        // Act
        $this->purchase->updateStatus();

        // Assert
        $this->assertEquals(0, $this->purchase->count_value);
        $this->assertEquals(StatusEnum::PAGAMENTO_PENDENTE->value, $this->purchase->status);
    }

    # php artisan test --filter=PurchaseTest::test_update_status_deve_definir_status_como_erro_estoque
    public function test_update_status_deve_definir_status_como_erro_estoque(): void
    {
        // Arrange
        $product = Product::factory()->create();
        $this->purchase->products()->attach($product->id, ['purchase_value' => 150, 'amount' => 1]);

        // Adiciona um pagamento de 100
        Payment::factory()->create([
            'payable_id' => $this->purchase->id,
            'payable_type' => Purchase::class,
            'value' => 100,
        ]);

        // Act
        $this->purchase->updateStatus();

        // Assert
        $this->assertEquals(-50, $this->purchase->count_value);
        $this->assertEquals(StatusEnum::ERRO_ESTOQUE->value, $this->purchase->status);
    }

  # php artisan test --filter=PurchaseTest::test_update_status_deve_definir_status_como_pendente
    public function test_update_status_deve_definir_status_como_pendente(): void
    {
        // Arrange
        $product = Product::factory()->create();
        $this->purchase->products()->attach($product->id, ['purchase_value' => 50, 'amount' => 1]);

        Payment::factory()->create([
            'payable_id' => $this->purchase->id,
            'payable_type' => Purchase::class,
            'value' => 100,
        ]);

        // Act
        $this->purchase->updateStatus();

        // Assert
        $this->assertEquals(50, $this->purchase->count_value);
        $this->assertEquals(StatusEnum::PENDENTE->value, $this->purchase->status);
    }
}
