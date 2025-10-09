<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\ProductPermissionEnum;
use App\Http\Controllers\Api\V1\Controller;
use App\Http\Requests\Api\V1\Product\StoreProductRequest;
use App\Http\Requests\Api\V1\Product\UpdateProductRequest;
use App\Http\Resources\V1\Product\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function index(): JsonResponse
    {
        $this->authorize(ProductPermissionEnum::INDEX);

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

        $validateData = $request->validated();

        $product = Product::create($validateData);

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
            'Usuário detalhado com sucesso!',
            200
        );
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $this->authorize(ProductPermissionEnum::UPDATE->value);

        $validateData = $request->validated();

        $product->update($validateData);

        return $this->successResponse(
            new ProductResource($product),
            'Usuário atualizado com sucesso!',
            200
        );
    }
}
