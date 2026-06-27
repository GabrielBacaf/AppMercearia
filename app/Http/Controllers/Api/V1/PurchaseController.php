<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\PurchasePermissionEnum;
use App\Http\Requests\Api\V1\Purchase\StorePurchaseRequest;
use App\Http\Requests\Api\V1\Purchase\UpdatePurchaseRequest;
use App\Http\Resources\V1\Purchase\PurchaseResource;
use App\Http\Services\PurchaseService;
use App\Models\Product;
use App\Models\Purchase;
use Exception;



class PurchaseController extends Controller
{

    public function __construct(protected PurchaseService $purchaseService) {}

    public function index(\Illuminate\Http\Request $request)
    {
        $this->authorize(PurchasePermissionEnum::INDEX->value);

        $query = Purchase::query();
        
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%");
            });
        }
        if ($request->filled('date_start')) {
            $query->whereDate('created_at', '>=', $request->input('date_start'));
        }
        if ($request->filled('date_end')) {
            $query->whereDate('created_at', '<=', $request->input('date_end'));
        }

        $purchase = $query->latest()->paginate(5);
        return $this->successResponseCollection(
            PurchaseResource::collection($purchase->load('payments')),
            $purchase,
            "Compras listados com sucesso!",
            200
        );
    }

    public function store(StorePurchaseRequest $request)
    {
        $this->authorize(PurchasePermissionEnum::STORE->value);

        try {

            $purchase = $this->purchaseService->storePurchase(
                $request->validated()
            );

            return $this->successResponse(
                new PurchaseResource($purchase->load('payments')),
                "Compra e pagamento inicial registrados com sucesso!",
                201
            );
        } catch (Exception $e) {
            return $this->errorResponse('Ocorreu um erro ao registrar a compra.', (array)$e->getMessage(), 500);
        }
    }

    public function show(Purchase $purchase)
    {
        $this->authorize(PurchasePermissionEnum::SHOW->value);

        return $this->successResponse(
            new PurchaseResource($purchase->load(['payments', 'products'])),
            'Compra detalhado com sucesso!',
            200
        );
    }


    public function update(UpdatePurchaseRequest $request, Purchase $purchase)
    {
        $this->authorize(PurchasePermissionEnum::UPDATE->value);

        try {

            $purchase = $this->purchaseService->updatePurchase(
                $request->validated(),
                $purchase
            );

            return $this->successResponse(
                new PurchaseResource($purchase->load('payments')),
                "Compra e pagamento inicial atualizados com sucesso!",
                200
            );
        } catch (Exception $e) {
            return $this->errorResponse('Ocorreu um erro ao atualizar a compra.', (array)$e->getMessage(), 500);
        }
    }


    public function destroy(Purchase $purchase)
    {
        $this->authorize(PurchasePermissionEnum::DESTROY->value);

        if ($purchase->products()->exists()) {
            return $this->errorResponse('A compra possui vinculo com produtos', [], 500);
        }

        $purchase->delete();
        return $this->successResponse([], 'Compra deletada com sucesso!', 200);
    }

    public function removeProduct(Purchase $purchase, Product $product)
    {
        $this->authorize(PurchasePermissionEnum::UPDATE->value); // Assuming update permission is enough

        try {
            $purchase->products()->detach($product->id);
            $purchase->updateStatus();

            return $this->successResponse(
                new PurchaseResource($purchase->load('payments', 'products')),
                'Produto desvinculado da compra com sucesso!',
                200
            );
        } catch (Exception $e) {
            return $this->errorResponse('Ocorreu um erro ao desvincular o produto.', (array)$e->getMessage(), 500);
        }
    }
}
