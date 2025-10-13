<?php

namespace App\Http\Services;

use App\Models\Product;
use App\Models\Purchase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class ProductService
{
    public function __construct()
    {
    }

    public function storeProduct(array $data): Product
    {
        $purchase = Purchase::findOrFail($data['purchase_id']);

        return DB::transaction(function () use ($data, $purchase) {

            $productData = collect($data)->except(['purchase_id', 'purchase_value', 'amount'])->all();
            $productData['stock_quantity'] = $data['amount'];
            $product = Product::create($productData);

            $pivotData = [
                'purchase_value' => $data['purchase_value'],
                'amount' => $data['amount'],
            ];
            $product->purchases()->attach($purchase->id, $pivotData);

            $purchase->updateStatus();

            return $product;
        });
    }

    public function updateProduct(array $data, Product $product): Product
    {

        $purchase = Purchase::findOrFail($data['purchase_id']);

        return DB::transaction(function () use ($data, $product, $purchase) {

            $productData = collect($data)->except(['purchase_id', 'purchase_value', 'amount'])->all();

            $pivotData = [
                'purchase_value' => $data['purchase_value'],
                'amount' => $data['amount'],
            ];

            $product->purchases()->syncWithoutDetaching([$purchase->id => $pivotData]);

            $product->fill($productData);

            $product->stock_quantity = $product->purchases()->sum('amount');

            $product->save();

            $purchase->updateStatus();

            return $product;
        });
    }
}
