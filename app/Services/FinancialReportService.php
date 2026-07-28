<?php

namespace App\Services;

use App\Models\AccountPayable;
use App\Models\AccountReceivable;
use App\Models\Sale;
use App\Enums\FinancialStatusEnum;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FinancialReportService
{
    public function getDashboardReport(int $month, int $year): array
    {
        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        // 1. Regime de Caixa (Realizado)
        $caixa = $this->getCaixaMetrics($startDate, $endDate);

        // 2. Regime de Competência (Projetado/Devido)
        $competencia = $this->getCompetenciaMetrics($startDate, $endDate);

        // 3. Vendas Diárias (Regime de Competência - quando a venda de fato ocorreu ou venceu)
        // Usamos AccountReceivable pela data de vencimento para refletir as vendas do mês
        $dailySales = $this->getDailySales($startDate, $endDate);

        // 4. Despesas por Categoria (Caixa - o que foi pago)
        $expensesByCategory = $this->getExpensesByCategory($startDate, $endDate);

        // 5. Performance de Produtos (Top Vendas, Mais Lucrativos e Prejuízos)
        $productPerformance = $this->getProductsPerformance($startDate, $endDate);

        // 6. Produtos Parados (Estoque Alto / Baixo Giro)
        $slowMovingProducts = $this->getSlowMovingProducts($startDate, $endDate);

        // 7. Indicador de Saúde (Health Indicator)
        $healthStatus = $this->calculateHealthStatus($caixa['lucro_liquido'], $caixa['total_receitas']);

        return [
            'period' => [
                'month' => $month,
                'year' => $year,
            ],
            'regime_caixa' => $caixa,
            'regime_competencia' => $competencia,
            'health_status' => $healthStatus,
            'charts' => [
                'daily_sales' => $dailySales,
                'expenses_by_category' => $expensesByCategory,
            ],
            'top_products' => [
                'by_volume' => $productPerformance['by_volume'],
                'by_profitability' => $productPerformance['by_profitability'],
            ],
            'loss_products' => $productPerformance['loss_products'],
            'slow_moving_products' => $slowMovingProducts,
        ];
    }

    private function getCaixaMetrics(Carbon $startDate, Carbon $endDate): array
    {
        $receitas = AccountReceivable::where('status', FinancialStatusEnum::RECEIVED)
            ->whereBetween('received_date', [$startDate, $endDate])
            ->sum('amount');

        $despesas = AccountPayable::where('status', FinancialStatusEnum::PAID)
            ->whereBetween('paid_date', [$startDate, $endDate])
            ->sum('amount');

        return [
            'total_receitas' => round((float) $receitas, 2),
            'total_despesas' => round((float) $despesas, 2),
            'lucro_liquido' => round((float) ($receitas - $despesas), 2),
        ];
    }

    private function getCompetenciaMetrics(Carbon $startDate, Carbon $endDate): array
    {
        $receitas = AccountReceivable::whereBetween('due_date', [$startDate, $endDate])
            ->sum('amount');

        $despesas = AccountPayable::whereBetween('due_date', [$startDate, $endDate])
            ->sum('amount');

        return [
            'projetado_receitas' => round((float) $receitas, 2),
            'projetado_despesas' => round((float) $despesas, 2),
            'lucro_projetado' => round((float) ($receitas - $despesas), 2),
        ];
    }

    private function getDailySales(Carbon $startDate, Carbon $endDate): array
    {
        // Agrupa pelo dia de vencimento (ou criacao da conta a receber)
        $sales = AccountReceivable::select(
            DB::raw('DATE(due_date) as date'),
            DB::raw('SUM(amount) as total')
        )
            ->whereBetween('due_date', [$startDate, $endDate])
            ->groupBy(DB::raw('DATE(due_date)'))
            ->orderBy('date')
            ->get();

        $daily = [];
        $currentDate = $startDate->copy();
        
        // Preenche dias vazios com 0
        while ($currentDate <= $endDate) {
            $dateString = $currentDate->format('Y-m-d');
            $daily[$dateString] = 0;
            $currentDate->addDay();
        }

        foreach ($sales as $sale) {
            $daily[$sale->date] = round((float) $sale->total, 2);
        }

        // Formata para o frontend (array de objetos)
        return collect($daily)->map(function ($total, $date) {
            return [
                'date' => $date,
                'total' => $total,
            ];
        })->values()->toArray();
    }

    private function getExpensesByCategory(Carbon $startDate, Carbon $endDate): array
    {
        $expenses = AccountPayable::select('type', DB::raw('SUM(amount) as total'))
            ->where('status', FinancialStatusEnum::PAID)
            ->whereBetween('paid_date', [$startDate, $endDate])
            ->groupBy('type')
            ->get();

        return $expenses->map(function ($expense) {
            $typeString = $expense->type instanceof \BackedEnum ? $expense->type->value : $expense->type;
            $enum = \App\Enums\AccountPayableTypeEnum::tryFrom($typeString);

            return [
                'category' => $enum ? $enum->value : $typeString,
                'label' => $enum ? $enum->label() : $typeString,
                'total' => round((float) $expense->total, 2),
            ];
        })->toArray();
    }

    private function getProductsPerformance(Carbon $startDate, Carbon $endDate): array
    {
        $salesQuery = Sale::whereBetween('created_at', [$startDate, $endDate]);
        $saleIds = $salesQuery->pluck('id');

        if ($saleIds->isEmpty()) {
            return ['by_volume' => [], 'by_profitability' => [], 'loss_products' => []];
        }

        // Tabela pivot: product_sale (sale_id, product_id, amount, sale_value)
        $productsData = DB::table('product_sale')
            ->join('products', 'product_sale.product_id', '=', 'products.id')
            ->whereIn('product_sale.sale_id', $saleIds)
            ->select(
                'products.id',
                'products.name',
                'product_sale.amount as quantity',
                'product_sale.sale_value'
            )
            ->get();

        // Get the latest purchase cost for all sold products
        $productIds = $productsData->pluck('id')->unique()->toArray();
        $latestPurchaseSubquery = DB::table('product_purchase')
            ->whereIn('product_id', $productIds)
            ->select('product_id', DB::raw('MAX(id) as max_id'))
            ->groupBy('product_id');

        $costs = DB::table('product_purchase')
            ->joinSub($latestPurchaseSubquery, 'latest_purchases', function ($join) {
                $join->on('product_purchase.id', '=', 'latest_purchases.max_id');
            })
            ->pluck('purchase_value', 'product_purchase.product_id');

        $aggregated = [];

        foreach ($productsData as $row) {
            if (!isset($aggregated[$row->id])) {
                $aggregated[$row->id] = [
                    'id' => $row->id,
                    'name' => $row->name,
                    'total_quantity' => 0,
                    'total_revenue' => 0,
                    'total_cost' => 0,
                    'total_profit' => 0,
                ];
            }

            $qty = (float) $row->quantity;
            $revenue = (float) $row->sale_value * $qty;
            
            // Custo baseado na última compra (se não houver, assume 0)
            $unitCost = (float) ($costs[$row->id] ?? 0);
            $cost = $unitCost * $qty;
            
            $profit = $revenue - $cost;

            $aggregated[$row->id]['total_quantity'] += $qty;
            $aggregated[$row->id]['total_revenue'] += $revenue;
            $aggregated[$row->id]['total_cost'] += $cost;
            $aggregated[$row->id]['total_profit'] += $profit;
        }

        $collection = collect($aggregated);

        // Top 5 by Volume
        $byVolume = $collection->sortByDesc('total_quantity')->take(5)->values()->map(function ($item) {
            return [
                'id' => $item['id'],
                'name' => $item['name'],
                'volume' => $item['total_quantity'],
                'revenue' => round($item['total_revenue'], 2),
            ];
        })->toArray();

        // Top 5 by Profitability
        $byProfitability = $collection->sortByDesc('total_profit')->take(5)->values()->map(function ($item) {
            $margin = $item['total_revenue'] > 0 
                ? ($item['total_profit'] / $item['total_revenue']) * 100 
                : 0;

            return [
                'id' => $item['id'],
                'name' => $item['name'],
                'profit' => round($item['total_profit'], 2),
                'margin_percentage' => round($margin, 2),
            ];
        })->toArray();
        
        // Produtos com Prejuízo (lucro < 0)
        $lossProducts = $collection->filter(function ($item) {
            return $item['total_profit'] < 0;
        })->sortBy('total_profit')->values()->map(function ($item) {
            return [
                'id' => $item['id'],
                'name' => $item['name'],
                'loss' => round($item['total_profit'], 2),
                'revenue' => round($item['total_revenue'], 2),
                'cost' => round($item['total_cost'], 2),
            ];
        })->toArray();

        return [
            'by_volume' => $byVolume,
            'by_profitability' => $byProfitability,
            'loss_products' => $lossProducts,
        ];
    }

    private function getSlowMovingProducts(Carbon $startDate, Carbon $endDate): array
    {
        // Produtos encalhados: Alto estoque atual, mas poucas ou zero vendas no período.
        // Buscar os top 20 produtos com maior estoque.
        $products = DB::table('products')
            ->where('stock_quantity', '>', 0)
            ->orderByDesc('stock_quantity')
            ->take(20)
            ->get();
            
        if ($products->isEmpty()) {
            return [];
        }
            
        $productIds = $products->pluck('id')->toArray();
        
        // Vendas do período para esses produtos
        $salesQuery = Sale::whereBetween('created_at', [$startDate, $endDate]);
        $saleIds = $salesQuery->pluck('id');
        
        $soldQuantities = [];
        if ($saleIds->isNotEmpty()) {
            $soldQuantities = DB::table('product_sale')
                ->whereIn('sale_id', $saleIds)
                ->whereIn('product_id', $productIds)
                ->select('product_id', DB::raw('SUM(amount) as total_sold'))
                ->groupBy('product_id')
                ->pluck('total_sold', 'product_id')
                ->toArray();
        }
        
        $slowMoving = [];
        
        foreach ($products as $product) {
            $sold = (int) ($soldQuantities[$product->id] ?? 0);
            
            // Taxa de giro: vendido / estoque (evitando divisão por zero)
            // Produtos que venderam menos de 10% do estoque no período, ou 0.
            $stock = $product->stock_quantity > 0 ? $product->stock_quantity : 1;
            $turnoverRate = $sold / $stock;
            
            if ($turnoverRate <= 0.1) {
                $slowMoving[] = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'stock_quantity' => $product->stock_quantity,
                    'sold_in_period' => $sold,
                    'turnover_rate_percentage' => round($turnoverRate * 100, 2),
                ];
            }
        }
        
        // Ordenar os com pior giro primeiro (mais estoque e menos venda)
        usort($slowMoving, function ($a, $b) {
            return $a['turnover_rate_percentage'] <=> $b['turnover_rate_percentage'] 
                ?: $b['stock_quantity'] <=> $a['stock_quantity']; // Desempate: maior estoque
        });
        
        return array_slice($slowMoving, 0, 10);
    }

    private function calculateHealthStatus(float $lucroLiquido, float $receitas): array
    {
        if ($receitas == 0) {
            return [
                'status' => 'warning',
                'message' => 'Sem receitas no período.',
                'margin_percentage' => 0,
            ];
        }

        $margin = ($lucroLiquido / $receitas) * 100;

        if ($lucroLiquido > 0) {
            $status = 'healthy';
            $message = 'Negócio Saudável! O lucro cobre as despesas do período.';
        } else {
            $status = 'danger';
            $message = 'Atenção: As despesas superaram as receitas realizadas no período.';
        }

        return [
            'status' => $status,
            'message' => $message,
            'margin_percentage' => round($margin, 2),
        ];
    }
}
