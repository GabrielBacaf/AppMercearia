<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\SupplierPermissionEnum;
use App\Http\Requests\Api\V1\Supplier\SupplierRequest;
use App\Http\Resources\V1\Supplier\SupplierResource;
use App\Models\Supplier;

class SupplierController extends Controller
{
    public function index()
    {
        $this->authorize(SupplierPermissionEnum::INDEX->value);

        $suppliers = Supplier::paginate(5);

        return $this->successResponseCollection(
            SupplierResource::collection($suppliers),
            $suppliers,
            "Fornecedores listados com sucesso!",
            200
        );
    }

    public function show(Supplier $supplier)
    {
        $this->authorize(SupplierPermissionEnum::SHOW->value);

        return $this->successResponse(
            new SupplierResource($supplier),
            'Fornecedor detalhado com sucesso!',
            200
        );
    }

    public function store(SupplierRequest $request) {
        $this->authorize(SupplierPermissionEnum::STORE->value);

        $supplier = Supplier::create($request->validated());

        return $this->successResponse(
            new SupplierResource($supplier),
            'Fornecedor cadastrado com sucesso!',
            201
        );
    }

    public function update(SupplierRequest $request, Supplier $supplier)
    {
        $this->authorize(SupplierPermissionEnum::UPDATE->value);

        $supplier->update($request->validated());

        return $this->successResponse(
            new SupplierResource($supplier),
            'Fornecedor atualizado com sucesso!',
            200
        );
    }


}
