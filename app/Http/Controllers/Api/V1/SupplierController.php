<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\SupplierPermissionEnum;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize(SupplierPermissionEnum::INDEX->value);

        return $this->successResponseCollection(
            SupplierResource::collection($suppliers),
            $suppliers,
            "Fornecedores listados com sucesso!",
            200
        );


    }
}
