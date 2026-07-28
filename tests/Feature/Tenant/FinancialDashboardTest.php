<?php

namespace Tests\Feature\Tenant;

use Tests\TestCase;
use App\Models\Tenant;
use App\Models\User;
use App\Models\AccountPayable;
use App\Models\AccountReceivable;
use App\Models\Sale;
use App\Models\Product;
use App\Enums\FinancialStatusEnum;
use App\Enums\AccountPayableTypeEnum;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FinancialDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected bool $initializeTenancy = false;

    protected function setUp(): void
    {
        parent::setUp();
        
        $tenantId = 'test-tenant-' . uniqid();
        $tenant = Tenant::create(['id' => $tenantId]);
        $tenant->domains()->create(['domain' => $tenantId . '.test']);
        tenancy()->initialize($tenant);
    }

    public function test_dashboard_returns_correct_caixa_metrics()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create();
        $this->actingAs($user);

        $now = Carbon::now();

        // Receitas no Caixa (Pagas no mes atual)
        AccountReceivable::factory()->create([
            'amount' => 1000,
            'status' => FinancialStatusEnum::RECEIVED,
            'received_date' => $now,
            'due_date' => $now,
        ]);
        AccountReceivable::factory()->create([
            'amount' => 500,
            'status' => FinancialStatusEnum::RECEIVED,
            'received_date' => $now,
            'due_date' => $now,
        ]);

        // Receita de outro mês não deve contar pro Caixa deste mes
        AccountReceivable::factory()->create([
            'amount' => 300,
            'status' => FinancialStatusEnum::RECEIVED,
            'received_date' => $now->copy()->subMonth(),
            'due_date' => $now->copy()->subMonth(),
        ]);

        // Despesas no Caixa
        AccountPayable::factory()->create([
            'amount' => 400,
            'status' => FinancialStatusEnum::PAID,
            'paid_date' => $now,
            'type' => AccountPayableTypeEnum::OPERATIONAL,
        ]);

        $domain = tenant()->domains->first()->domain;
        $response = $this->getJson("http://{$domain}/api/v1/financial/dashboard?month={$now->month}&year={$now->year}");

        $response->assertStatus(200);
        $response->assertJsonPath('data.regime_caixa.total_receitas', 1500);
        $response->assertJsonPath('data.regime_caixa.total_despesas', 400);
        $response->assertJsonPath('data.regime_caixa.lucro_liquido', 1100);
    }

    public function test_dashboard_returns_correct_top_products_and_new_metrics()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create();
        $this->actingAs($user);

        $now = Carbon::now();

        $categoryAlimentos = \App\Models\Category::create(['name' => 'Alimentos']);
        $categoryUtilidades = \App\Models\Category::create(['name' => 'Utilidades']);

        // Produtos
        $productArroz = Product::factory()->create([
            'name' => 'Arroz',
            'category_id' => $categoryAlimentos->id,
            'sale_value' => 22, // Cost will be 25, Profit will be -3
            'stock_quantity' => 100,
        ]);

        $productPilha = Product::factory()->create([
            'name' => 'Pilha',
            'category_id' => $categoryUtilidades->id,
            'sale_value' => 20, // Cost will be 10, Profit will be 10
            'stock_quantity' => 50,
        ]);
        
        $productTomate = Product::factory()->create([
            'name' => 'Tomate',
            'category_id' => $categoryAlimentos->id,
            'sale_value' => 5,
            'stock_quantity' => 200, // Alto estoque, sem vendas (encalhado)
        ]);
        
        // Simular uma compra para definir o custo real dos produtos
        $supplierId = \Illuminate\Support\Facades\DB::table('suppliers')->insertGetId([
            'fantasy_name' => 'Fornecedor Teste',
            'legal_name' => 'Fornecedor Teste LTDA',
            'cnpj' => '00000000000000',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        
        $purchaseId = \Illuminate\Support\Facades\DB::table('purchases')->insertGetId([
            'title' => 'Compra Teste',
            'supplier_id' => $supplierId,
            'user_id' => $user->id,
            'count_value' => 0,
            'status' => 'paid',
            'purchase_date' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        \Illuminate\Support\Facades\DB::table('product_purchase')->insert([
            ['purchase_id' => $purchaseId, 'product_id' => $productArroz->id, 'amount' => 100, 'purchase_value' => 25, 'created_at' => $now, 'updated_at' => $now],
            ['purchase_id' => $purchaseId, 'product_id' => $productPilha->id, 'amount' => 50, 'purchase_value' => 10, 'created_at' => $now, 'updated_at' => $now],
            ['purchase_id' => $purchaseId, 'product_id' => $productTomate->id, 'amount' => 200, 'purchase_value' => 2, 'created_at' => $now, 'updated_at' => $now],
        ]);

        // Venda 1: Alto Volume de Arroz (que dará prejuízo)
        $sale1Id = \Illuminate\Support\Facades\DB::table('sales')->insertGetId([
            'discount' => 0, 'total_value' => 2200, 'delivery_price' => 0, 'user_id' => $user->id, 'created_at' => $now, 'updated_at' => $now
        ]);
        \Illuminate\Support\Facades\DB::table('product_sale')->insert([
            'sale_id' => $sale1Id, 'product_id' => $productArroz->id, 'amount' => 100, 'sale_value' => 22, 'created_at' => $now, 'updated_at' => $now
        ]);

        // Venda 2: Baixo volume de Pilha, mas alta margem (lucro)
        $sale2Id = \Illuminate\Support\Facades\DB::table('sales')->insertGetId([
            'discount' => 0, 'total_value' => 600, 'delivery_price' => 0, 'user_id' => $user->id, 'created_at' => $now, 'updated_at' => $now
        ]);
        \Illuminate\Support\Facades\DB::table('product_sale')->insert([
            'sale_id' => $sale2Id, 'product_id' => $productPilha->id, 'amount' => 30, 'sale_value' => 20, 'created_at' => $now, 'updated_at' => $now
        ]);

        $domain = tenant()->domains->first()->domain;
        $response = $this->getJson("http://{$domain}/api/v1/financial/dashboard?month={$now->month}&year={$now->year}");
        $response->assertStatus(200);

        // Top 5 by Volume
        $byVolume = $response->json('data.top_products.by_volume');
        $this->assertEquals('Arroz', $byVolume[0]['name']);
        $this->assertEquals(100, $byVolume[0]['volume']);
        
        $this->assertEquals('Pilha', $byVolume[1]['name']);
        $this->assertEquals(30, $byVolume[1]['volume']);

        // Top 5 by Profitability
        $byProfit = $response->json('data.top_products.by_profitability');
        $this->assertEquals('Pilha', $byProfit[0]['name']); // Pilha deu +300 de lucro
        $this->assertEquals(300, $byProfit[0]['profit']);
        
        // Loss Products
        $lossProducts = $response->json('data.loss_products');
        $this->assertCount(1, $lossProducts);
        $this->assertEquals('Arroz', $lossProducts[0]['name']);
        $this->assertEquals(-300, $lossProducts[0]['loss']); // 100 * 22 (revenue) - 100 * 25 (cost)
        $this->assertEquals(2200, $lossProducts[0]['revenue']);
        $this->assertEquals(2500, $lossProducts[0]['cost']);
        
        // Slow Moving Products
        $slowMoving = $response->json('data.slow_moving_products');
        // Tomate tem 200 de estoque e 0 vendas -> taxa de giro 0%.
        $this->assertNotEmpty($slowMoving);
        $this->assertEquals('Tomate', $slowMoving[0]['name']);
        $this->assertEquals(200, $slowMoving[0]['stock_quantity']);
        $this->assertEquals(0, $slowMoving[0]['sold_in_period']);
        $this->assertEquals(0, $slowMoving[0]['turnover_rate_percentage']);
    }
}
