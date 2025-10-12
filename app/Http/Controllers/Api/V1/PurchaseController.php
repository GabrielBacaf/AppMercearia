<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\PurchasePermissionEnum;
use App\Http\Requests\Api\V1\Purchase\StorePurchaseRequest;
use App\Http\Requests\Api\V1\Purchase\UpdatePurchaseRequest;
use App\Http\Resources\V1\Purchase\PurchaseResource;
use App\Http\Services\PurchaseService;
use App\Models\Purchase;
use Exception;
use function PHPUnit\Framework\isNull;



class PurchaseController extends Controller
{

    public function __construct(protected PurchaseService $purchaseService) {}

    public function index()
    {
        $this->authorize(PurchasePermissionEnum::INDEX->value);

        $purchase = Purchase::paginate(5);
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
            new PurchaseResource($purchase->load('payments')),
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
                201
            );
        } catch (Exception $e) {
            return $this->errorResponse('Ocorreu um erro ao atualizar a compra.', (array)$e->getMessage(), 500);
        }
    }


    public function destroy(Purchase $purchase)
    {
        $this->authorize(PurchasePermissionEnum::DESTROY->value);

        $validateData = $purchase->products() ?? null;

        if (isNull($validateData)) {
            $purchase->delete();
            return $this->successResponse([], 'Compra deletado com sucesso!', 200);
        }

        return $this->errorResponse('A compra possui vinculo com produtos', [], 500);
    }
}
