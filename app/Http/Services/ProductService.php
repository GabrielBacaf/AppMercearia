<?php

namespace App\Http\Services;

use App\Models\Product;
use App\Models\Purchase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class ProductService
{
    public function __construct() {}

    public function storeProduct(array $data): Product
    {
        $purchase = Purchase::findOrFail($data['purchase_id']);

        return DB::transaction(function () use ($data, $purchase) {

            $productData = collect($data)->except(['purchase_id', 'purchase_value'])->all();

            $product = Product::create($productData);


            $purchase->products()->attach($product->id, [
                'purchase_value' => $data['purchase_value'],
                'amount' => $data['stock_quantity'],
            ]);


            $purchase->updateStatus();

            return $product;
        });
    }

    public function updateProduct(array $data, Product $product): Product
    {

        return DB::transaction(function () use ($data, $product) {

            $purchase = Purchase::findOrFail($data['purchase_id']);

            $productData = collect($data)->except(['purchase_id', 'purchase_value'])->all();

            $product->update($productData);

            $product->purchases()->syncWithoutDetaching([
                $purchase->id => [
                    'purchase_value' => $data['purchase_value'],
                    'amount' => $data['stock_quantity'],
                ],
            ]);

            $purchase->updateStatus();

            return $product;
        });
    }
}
