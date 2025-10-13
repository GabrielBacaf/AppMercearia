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
            'expiration_date' => ['sometimes', 'nullable', 'date', 'date_format:Y-m-d', 'after_or_equal:today'],
            'sale_value' => ['required', 'numeric', 'min:0'],
            'category' => ['required', 'string', Rule::in(CategoryEnum::values())],
            'stock_quantity' => ['prohibited'],
            'amount' => ['required', 'integer', 'min:0'],
            'purchase_id' => ['required', 'integer', 'exists:purchases,id'],
            'purchase_value' => ['required', 'numeric'],
        ];
    }

    public function attributes(): array
    {
        return [
            'barcode' => 'Código de Barras',
            'name' => 'Nome do Produto',
            'expiration_date' => 'Data de Validade',
            'sale_value' => 'Preço de venda',
            'category' => 'Categoria',
            'purchase_value' => 'Valor de Compra',
            'purchase_id' => 'Compra',
            'amount' => 'Quantidade',
            'stock_quantity' => 'Quantidade em Estoque',
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
