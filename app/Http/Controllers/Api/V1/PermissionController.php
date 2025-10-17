<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\PermissionEnum;
use App\Enums\ProductPermissionEnum;
use App\Http\Requests\Api\V1\Product\StoreProductRequest;
use App\Http\Requests\Api\V1\Product\UpdateProductRequest;
use App\Http\Resources\V1\Permission\PermissionResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function index(): JsonResponse
    {
        $this->authorize(PermissionEnum::INDEX->value);

        $permissions = Permission::paginate(5);

        return $this->successResponseCollection(
            PermissionResource::collection($permissions),
            $permissions,
            "Permissões listados com sucesso!",
            200
        );
    }

    public function store(StoreProductRequest $request)
    {
        $this->authorize(ProductPermissionEnum::STORE->value);

        $validateData = $request->validated();

        $produto = Product::create($validateData);

        return $this->successResponse($produto, 'Produto cadastrado com sucesso!', 201);
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $this->authorize(ProductPermissionEnum::UPDATE->value);

    }
}
