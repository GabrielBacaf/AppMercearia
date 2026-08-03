<?php

namespace App\Http\Requests\Api\V1\Product;

use App\Enums\CategoryEnum;
use App\Enums\ProductPermissionEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can(ProductPermissionEnum::STORE->value);
    }

    public function rules(): array
    {
        return [
            'barcode' => ['required', 'string', 'max:14', Rule::unique('products', 'barcode')],
            'name' => ['required', 'string', 'max:255', Rule::unique('products', 'name')],
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
