<?php

namespace App\Http\Requests\Api\V1\Purchase;

use App\Enums\PurchasePermissionEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;

class PurchaseProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can(PurchasePermissionEnum::UPDATE->value);
    }

    public function rules(): array
    {
        $rules = [
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'purchase_value' => ['required', 'numeric', 'min:0'],
            'expiration_date' => ['sometimes', 'date', 'after_or_equal:today'],
        ];

        if ($this->isMethod('post')) {
            $rules['product_id'] = ['required', 'integer', Rule::exists('products', 'id')];
        }

        return $rules;
    }

    public function attributes(): array
    {
        return [
            'product_id' => 'Produto',
            'stock_quantity' => 'Quantidade no Estoque',
            'purchase_value' => 'Valor de Compra',
            'expiration_date' => 'Data de Validade',
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
