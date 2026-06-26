<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\SalePermissionEnum;
use App\Enums\SalesPermissionEnum;
use App\Http\Requests\Api\V1\Sale\StoreSaleRequest;
use App\Http\Requests\Api\V1\Sale\UpdateSaleRequest;
use App\Http\Resources\V1\Sale\SaleResource;
use App\Http\Services\SaleService;
use App\Models\Sale;

class SaleController extends Controller
{
    public function __construct(private SaleService $service) {}
    public function index(\Illuminate\Http\Request $request)
    {
        $this->authorize(SalePermissionEnum::INDEX->value);

        $query = Sale::with('products', 'client', 'payments');
        
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('client', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }
        if ($request->filled('date_start')) {
            $query->whereDate('created_at', '>=', $request->input('date_start'));
        }
        if ($request->filled('date_end')) {
            $query->whereDate('created_at', '<=', $request->input('date_end'));
        }

        $sales = $query->paginate(5);
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

        try {

            $sale = $this->service->store($request->validated());

            return $this->successResponse(
                new SaleResource($sale),
                "Venda criada com sucesso!",
                201
            );
        } catch (\Exception $e) {

            return $this->errorResponse(
                $e->getMessage(),
                [],
                400
            );
        }
    }

    public function show($id)
    {
        $this->authorize(SalePermissionEnum::INDEX->value);

        $sale = Sale::with('products', 'client', 'payments')->findOrFail($id);

        return $this->successResponse(
            new SaleResource($sale),
            "Venda retornada com sucesso!",
            200
        );
    }

    public function update(UpdateSaleRequest $request, $id)
    {
        $this->authorize(SalePermissionEnum::UPDATE->value);

        try {
            $sale = Sale::findOrFail($id);
            $sale = $this->service->update($sale, $request->validated());

            return $this->successResponse(
                new SaleResource($sale),
                "Venda atualizada com sucesso!",
                200
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                $e->getMessage(),
                [],
                400
            );
        }
    }
}
