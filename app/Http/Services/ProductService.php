<?php

namespace App\Http\Services;

use App\Exceptions\Product\ProductNotFoundException;
use App\Models\Product;
use App\Models\Purchase;

use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Support\Collection;

class ProductService
{
    public function __construct() {}

    public function storeProduct(array $data): Product
    {
        return Product::create($data);
    }

    public function updateProduct(array $data, Product $product): Product
    {
        $product->update($data);
        return $product;
    }


    public function deductStock(array $products): Collection
    {
        $productIds = array_column($products, 'id');

        $lockedProducts = Product::lockedByIds($productIds)->get()->keyBy('id');


        return collect($products)->map(function (array $productData) use ($lockedProducts) {
            return $this->processSingleProductDeduction($productData, $lockedProducts);
        });
    }


    private function processSingleProductDeduction(array $productData, Collection $lockedProducts): Product
    {
        $productId = $productData['id'] ?? null;

        $product = $lockedProducts->get($productId);

        throw_unless(
            $product,
            ProductNotFoundException::class,
            (int) $productId
        );

        $product->deductStock($productData['quantity'] ?? 0);

        return $product;
    }
}
