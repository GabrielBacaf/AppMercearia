<?php

namespace App\Http\Requests\Api\V1\Product;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\CategoryEnum;
use App\Enums\ProductPermissionEnum;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can(ProductPermissionEnum::UPDATE->value);
    }

    public function rules(): array
    {
        $productId = $this->route('product')->id;
        return [
            'barcode' => ['required', 'string', 'max:14', Rule::unique('products', 'barcode')->ignore($productId)],
            'name' => ['required', 'string', 'max:255', Rule::unique('products', 'name')->ignore($productId)],
            'sale_value' => ['required', 'numeric', 'min:0'],
            'category_id' => ['required', Rule::exists('categories', 'id')],
        ];
    }

    public function attributes(): array
    {
        return [
            'barcode' => 'Código de Barras',
            'name' => 'Nome do Produto',
            'sale_value' => 'Preço de venda',
            'category_id' => 'Categoria',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Erros de validação foram encontrados.',
            'errors' => $validator->errors()
        ], 422));
    }
}
