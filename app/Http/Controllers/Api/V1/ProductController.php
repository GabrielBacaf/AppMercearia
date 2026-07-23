<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\ProductPermissionEnum;
use App\Http\Controllers\Api\V1\Controller;
use App\Http\Requests\Api\V1\Product\StoreProductRequest;
use App\Http\Requests\Api\V1\Product\UpdateProductRequest;
use App\Http\Resources\V1\Product\ProductResource;
use App\Http\Services\ProductService;
use App\Models\Product;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function __construct(protected ProductService $productService) {}

    public function index(\Illuminate\Http\Request $request): JsonResponse
    {
        $this->authorize(ProductPermissionEnum::INDEX->value);

        $query = Product::query();
        
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        if ($request->has('barcode')) {
            $query->where('barcode', $request->barcode);
        }

        $products = $query->latest()->paginate(5);

        return $this->successResponseCollection(
            ProductResource::collection($products),
            $products,
            'Produtos listados com sucesso!',
            200
        );
    }
    public function store(StoreProductRequest $request)
    {
        $this->authorize(ProductPermissionEnum::STORE->value);

        $product = $this->productService->storeProduct($request->validated());

        return $this->successResponse(
            new ProductResource($product),
            'Produto criado com sucesso!',
            201
        );
    }

    public function show(Product $product)
    {
        $this->authorize(ProductPermissionEnum::SHOW->value);

        return $this->successResponse(
            new ProductResource($product),
            'Produto detalhado com sucesso!',
            200
        );
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $this->authorize(ProductPermissionEnum::UPDATE->value);

        $product = $this->productService->updateProduct($request->validated(), $product);

        return $this->successResponse(
            new ProductResource($product),
            'Produto atualizado com sucesso!',
            200
        );
    }

    public function destroy(Product $product)
    {
        $this->authorize(ProductPermissionEnum::DESTROY->value);

        $product->delete();

        return $this->successResponse(
            null,
            'Produto excluído com sucesso!',
            200
        );
    }
}
