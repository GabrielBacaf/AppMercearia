<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\Permissions\PermissionEnum;
use App\Enums\Permissions\ProductPermissionEnum;
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

        $permissions = Permission::select('id', 'name')->get();

        return response()->json([
            'success' => true,
            'message' => 'Permissões listadas com sucesso!',
            'data'    => $permissions
        ], 200);
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
