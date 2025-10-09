<?php

namespace App\Http\Controllers;

use App\Enums\ProductPermissionEnum;
use App\Http\Controllers\Api\V1\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $this->authorize(ProductPermissionEnum::INDEX);

        $products =  Product::all()->paginate(10);

        return $this->successResponse($products, 'Produtos listados com sucesso!', 200);
    }
}
