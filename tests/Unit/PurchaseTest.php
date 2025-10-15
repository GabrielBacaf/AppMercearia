<?php

namespace Tests\Unit;

use App\Enums\PaymentStatusEnum;
use App\Enums\StatusEnum;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Purchase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

# php artisan test --filter=PurchaseTest
class PurchaseTest extends TestCase
{
    use RefreshDatabase;

    private Purchase $purchase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->purchase = Purchase::factory()->create();
    }


    public function test_update_status_deve_definir_status_como_finalizado(): void
    {
        // Arrange
        // Adiciona um produto à compra com valor 100
        $product = Product::factory()->create();
        $this->purchase->products()->attach($product->id, ['purchase_value' => 100, 'amount' => 1]);

        // Adiciona um pagamento PAGO de 100
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


    public function test_update_status_deve_definir_status_como_pagamento_pendente(): void
    {
        // Arrange
        // Adiciona um produto à compra com valor 200
        $product = Product::factory()->create();
        $this->purchase->products()->attach($product->id, ['purchase_value' => 200, 'amount' => 1]);

        // Adiciona um pagamento PAGO de 100 e um DEVENDO de 100
        Payment::factory()->create([
            'payable_id' => $this->purchase->id,
            'payable_type' => Purchase::class,
            'value' => 100,
            'payment_status' => PaymentStatusEnum::PAGO->value,
        ]);
        Payment::factory()->create([
            'payable_id' => $this->purchase->id,
            'payable_type' => Purchase::class,
            'value' => 100,
            'payment_status' => PaymentStatusEnum::DEVENDO->value,
        ]);

        // Act
        $this->purchase->updateStatus();

        // Assert
        $this->assertEquals(0, $this->purchase->count_value);
        $this->assertEquals(StatusEnum::PAGAMENTO_PENDENTE->value, $this->purchase->status);
    }


    public function test_update_status_deve_definir_status_como_erro_estoque(): void
    {
        // Arrange
        // Adiciona um produto à compra com valor 150
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

    /**
     * Testa se o status é definido como PENDENTE quando o valor pago é maior que o custo dos produtos.
     */
    public function test_update_status_deve_definir_status_como_pendente(): void
    {
        // Arrange
        // Adiciona um produto à compra com valor 50
        $product = Product::factory()->create();
        $this->purchase->products()->attach($product->id, ['purchase_value' => 50, 'amount' => 1]);

        // Adiciona um pagamento de 100
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
