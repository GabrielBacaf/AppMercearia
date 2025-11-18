<?php

namespace App\Http\Requests\Api\V1\Supplier;

use App\Enums\SupplierPermissionEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;

class SupplierRequest extends FormRequest
{

    public function authorize(): bool
    {
        return $this->user()?->can(SupplierPermissionEnum::STORE->value);
    }

    public function rules(): array
    {


        return [
            'fantasy_name' => ['required', 'string', 'max:60'],
            'legal_name' => ['required', 'string', 'max:70', Rule::unique('suppliers', 'legal_name')->ignore($this->supplier)],
            'cnpj' => ['sometimes', 'string', 'max:14' , Rule::unique('suppliers', 'cnpj')->ignore($this->id)->ignore($this->supplier)],

        ];
    }

    public function attributes(): array
    {
        return [
            'fantasy_name' => 'Nome Fantasia',
            'legal_name' => 'Razão Social',
            'cnpj' => 'CNPJ',
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
