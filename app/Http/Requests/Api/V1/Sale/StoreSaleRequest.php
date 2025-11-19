<?php

namespace App\Http\Requests\Api\V1\Role;

use App\Enums\SalePermissionEnum;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;


class StoreSaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can(SalePermissionEnum::STORE->value);
    }

    public function rules(): array
    {
        return [
            'discount' => ['sometimes', 'decimal'],
            'delivery_price' => ['sometimes', 'decimal'],
            'user_id' => ['prohibited'],
            'updated_by' => ['prohibited'],
            'client_id' => ['sometimes', 'integer', 'exists:clients,id'],
            'products' => ['required', 'array', 'min:1'],
            'products.*' => ['sometimes', 'string', Rule::exists('id', 'products')]
        ];
    }

    public function attributes(): array
    {
        return [
            'discount' => 'DESCONTO',
            'delivery_price' => 'PREÇO DE ENTREGA',
            'user_id' => 'USUÁRIO',
            'updated_by' => 'USUARIO',
            'client_id' => 'CLIENTE',
            'products' => 'PRODUTO'

        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Erro de validação',
            'errors' => $validator->errors()
        ], 422));
    }
}
