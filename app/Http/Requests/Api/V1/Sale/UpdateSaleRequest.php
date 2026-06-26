<?php

namespace App\Http\Requests\Api\V1\Sale;

use App\Enums\SalePermissionEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class UpdateSaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can(SalePermissionEnum::UPDATE->value) ?? true;
    }

    public function rules(): array
    {
        return [
            'discount' => ['sometimes', 'numeric', 'min:0' , 'max:10'],
            'delivery_price' => ['sometimes', 'numeric', 'min:0'],
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
