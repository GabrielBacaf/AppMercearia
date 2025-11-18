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

    public function index(): JsonResponse
    {
        $this->authorize(ProductPermissionEnum::INDEX->value);

        $products = Product::paginate(5);

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

        try {
            $product = $this->productService->storeProduct($request->validated());

            return $this->successResponse(
                new ProductResource($product),
                'Produto criado com sucesso!',
                201
            );
        } catch (Exception $e) {
            return $this->errorResponse('Ocorreu um erro ao criar o produto.', (array) $e->getMessage(), 500);
        }
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

        try {
            $product = $this->productService->updateProduct($request->validated(), $product);

            return $this->successResponse(
                new ProductResource($product),
                'Produto atualizado com sucesso!',
                200
            );
        } catch (Exception $e) {
            return $this->errorResponse('Ocorreu um erro ao atualizar o produto.', (array) $e->getMessage(), 500);
        }
    }

    public function destroy(Product $product)
    {
        $this->authorize(ProductPermissionEnum::DESTROY->value);

        try {

            $product->delete();

            return $this->successResponse(
                null,
                'Produto excluído com sucesso!',
                200
            );
        } catch (Exception $e) {
            Log::error('Erro ao excluir produto', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'product_id' => $product->id,
            ]);

            return $this->errorResponse('Erro ao excluir produto.', [], 500);
        }
    }
}
