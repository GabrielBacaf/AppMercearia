<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\Permissions\SalePermissionEnum;
use App\Enums\Permissions\SalesPermissionEnum;
use App\Http\Requests\Api\V1\Sale\StoreSaleRequest;
use App\Http\Resources\V1\Sale\SaleResource;
use App\Http\Services\SaleService;
use App\Models\Sale;

class SaleController extends Controller
{
    public function __construct(private SaleService $service) {}
    public function index()
    {
        $this->authorize(SalePermissionEnum::INDEX->value);

        $sales = Sale::with('product', 'client')->paginate(5);
        return $this->successResponseCollection(
            SaleResource::collection($sales),
            $sales,
            "Vendas listadas com sucesso!",
            200
        );
    }
    public function store(StoreSaleRequest $request)
    {
        $this->authorize(SalePermissionEnum::STORE->value);

        $sale = $this->service->store($request->validated());

        return $this->successResponse(
            new SaleResource($sale),
            "Venda criada com sucesso!",
            201
        );
    }
}
