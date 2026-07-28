<?php

namespace App\Http\Services;

use App\Models\Product;
use Illuminate\Support\Facades\DB;
use App\Models\Sale;
use Exception;

class SaleService
{
    public function __construct(
        protected PaymentService $paymentService,
        protected AccountReceivableService $receivableService
    ) {}

    public function store(array $data)
    {
        return DB::transaction(function () use ($data) {

            $productsInfo = Product::updateStock($data['products'])->keyBy('id');

            $subTotal = 0;
            $pivotData = [];

            foreach ($data['products'] as $item) {
                $productId = $item['id'];
                $quantity = $item['quantity'];
                $currentPrice = $productsInfo[$productId]->sale_value;

                $subTotal += ($currentPrice * $quantity);

                $pivotData[$productId] = [
                    'amount'     => $quantity,
                    'sale_value' => $currentPrice
                ];
            }

            $discount = $data['discount'] ?? 0;
            $delivery = $data['delivery_price'] ?? 0;

            $data['total_value'] = ($subTotal + $delivery) - $discount;

            $sale = Sale::create($data);
            $sale->products()->sync($pivotData);

            $installments = $data['installments'] ?? 1;
            
            // Generate Accounts Receivable
            $this->receivableService->generateFromSale($sale, $installments);

            // Fetch the first receivable to apply immediate payment if provided
            $firstReceivable = $sale->accountsReceivable()->orderBy('due_date', 'asc')->first();

            if ($firstReceivable && !empty($data['payments'])) {
                // Here we assume the provided payment is for the first installment.
                // In a real scenario, we might iterate over payments.
                foreach ($data['payments'] as $paymentData) {
                    $this->receivableService->settle($firstReceivable, $paymentData);
                }
            }

            $sale->load('products', 'accountsReceivable.payments');

            return $sale;
        });
    }

    public function update(Sale $sale, array $data)
    {
        return DB::transaction(function () use ($sale, $data) {
            
            if (isset($data['discount']) || isset($data['delivery_price'])) {
                $discount = $data['discount'] ?? $sale->discount;
                $delivery = $data['delivery_price'] ?? $sale->delivery_price;
                
                $sale->load('products');
                
                $subTotal = $sale->products->sum(function($product) {
                    return $product->pivot->amount * $product->pivot->sale_value;
                });
                
                $data['total_value'] = ($subTotal + $delivery) - $discount;
            }
            
            $sale->update($data);
            return $sale;
        });
    }
}
