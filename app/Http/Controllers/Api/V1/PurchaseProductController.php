<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\PurchasePermissionEnum;
use App\Http\Requests\Api\V1\Purchase\PurchaseProductRequest;
use App\Http\Resources\V1\Product\ProductResource;
use App\Http\Services\PurchaseProductService;
use App\Models\Product;
use App\Models\Purchase;
use Illuminate\Http\JsonResponse;

class PurchaseProductController extends Controller
{
    public function __construct(protected PurchaseProductService $purchaseProductService) {}
   
    public function store(PurchaseProductRequest $request, Purchase $purchase): JsonResponse
    {
        $this->authorize(PurchasePermissionEnum::UPDATE->value);

        $data = $request->validated();
        
        $product = Product::findOrFail($data['product_id']);

        $product = $this->purchaseProductService->linkProductToPurchase($purchase, $product, $data);

        return $this->successResponse(
            new ProductResource($product),
            'Produto vinculado à compra com sucesso!',
            201
        );
    }

   
    public function update(PurchaseProductRequest $request, Purchase $purchase, Product $product): JsonResponse
    {
        $this->authorize(PurchasePermissionEnum::UPDATE->value);
        
        $data = $request->validated();

        $product = $this->purchaseProductService->linkProductToPurchase($purchase, $product, $data);

        return $this->successResponse(
            new ProductResource($product),
            'Dados do produto na compra atualizados com sucesso!',
            200
        );
    }

   
    public function destroy(Purchase $purchase, Product $product): JsonResponse
    {
        $this->authorize(PurchasePermissionEnum::UPDATE->value);

        $this->purchaseProductService->detachProductFromPurchase($purchase, $product);

        return $this->successResponse(
            null,
            'Produto desvinculado da compra com sucesso!',
            200
        );
    }
}
