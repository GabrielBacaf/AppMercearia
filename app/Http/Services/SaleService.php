<?php

namespace App\Http\Services;

use App\Models\Payment;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use App\Models\Sale;
use Exception;


class SaleService
{
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

            Payment::syncPayments($sale, $data['payments'] ?? []);

            $sale->load('products', 'payments');

            return $sale;
        });
    }
}
