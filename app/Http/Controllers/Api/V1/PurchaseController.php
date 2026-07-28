<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\PurchasePermissionEnum;
use App\Http\Requests\Api\V1\Purchase\StorePurchaseRequest;
use App\Http\Requests\Api\V1\Purchase\UpdatePurchaseRequest;
use App\Http\Resources\V1\Purchase\PurchaseResource;
use App\Http\Services\PurchaseService;
use App\Models\Purchase;
use Exception;



class PurchaseController extends Controller
{

    public function __construct(protected PurchaseService $purchaseService) {}

    public function index()
    {
        $this->authorize(PurchasePermissionEnum::INDEX->value);

        $purchases = Purchase::with('accountsPayable.payments')->paginate(5);
        return $this->successResponseCollection(
            PurchaseResource::collection($purchases),
            $purchases,
            "Compras listadas com sucesso!",
            200
        );
    }

    public function store(StorePurchaseRequest $request)
    {
        $this->authorize(PurchasePermissionEnum::STORE->value);

        $purchase = $this->purchaseService->storePurchase(
            $request->validated()
        );

        return $this->successResponse(
            new PurchaseResource($purchase->load('accountsPayable.payments')),
            "Compra e pagamento inicial registrados com sucesso!",
            201
        );
    }

    public function show(Purchase $purchase)
    {
        $this->authorize(PurchasePermissionEnum::SHOW->value);

        return $this->successResponse(
            new PurchaseResource($purchase->load('accountsPayable.payments')),
            'Compra detalhado com sucesso!',
            200
        );
    }


    public function update(UpdatePurchaseRequest $request, Purchase $purchase)
    {
        $this->authorize(PurchasePermissionEnum::UPDATE->value);

        $purchase = $this->purchaseService->updatePurchase(
            $request->validated(),
            $purchase
        );

        return $this->successResponse(
            new PurchaseResource($purchase->load('accountsPayable.payments')),
            "Compra e pagamento inicial atualizados com sucesso!",
            200
        );
    }


    public function destroy(Purchase $purchase)
    {
        $this->authorize(PurchasePermissionEnum::DESTROY->value);

        if ($purchase->products()->count() === 0) {
            $purchase->delete();
            return $this->successResponse([], 'Compra deletada com sucesso!', 200);
        }

        return $this->errorResponse('A compra possui vínculo com produtos', [], 400);
    }
}
