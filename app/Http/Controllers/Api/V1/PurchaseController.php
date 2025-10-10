<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\PurchasePermissionEnum;
use App\Enums\StatusEnum;
use App\Http\Requests\Api\V1\Purchase\StorePurchaseRequest;
use App\Http\Requests\Api\V1\Purchase\UpdatePurchaseRequest;
use App\Http\Resources\V1\Purchase\PurchaseResource;
use App\Models\Purchase;
use Illuminate\Http\Request;
use LaravelLang\Publisher\Console\Update;
use function PHPUnit\Framework\isNull;

class PurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize(PurchasePermissionEnum::INDEX->value);

        $purchase = Purchase::paginate(5);
        return $this->successResponseCollection(
            PurchaseResource::collection($purchase),
            $purchase,
            "Compras listados com sucesso!",
            200
        );
    }

    public function store(StorePurchaseRequest $request)
    {
        $this->authorize(PurchasePermissionEnum::STORE->value);

        $validateData = $request->validated();

        $purchase = Purchase::create(
            array_merge(
                $validateData,
                [
                    'status' => StatusEnum::PENDENTE
                ]
            )
        );

        return $this->successResponse(
            new PurchaseResource($purchase),
            "Compra Registrada com sucesso!",
            201
        );
    }


    public function show(Purchase $purchase)
    {
        $this->authorize(PurchasePermissionEnum::SHOW->value);

        return $this->successResponse(
            new PurchaseResource($purchase),
            'Compra detalhado com sucesso!',
            200
        );
    }


    public function update(UpdatePurchaseRequest $request, Purchase $purchase)
    {
        $this->authorize(PurchasePermissionEnum::UPDATE->value);

        $validateData = $request->validated();

        $purchase->update($validateData);

        return $this->successResponse(
            new PurchaseResource($purchase),
            "Compra Atualizada com sucesso!",
            200
        );
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
