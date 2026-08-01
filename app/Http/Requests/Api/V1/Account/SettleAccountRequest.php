<?php

namespace App\Http\Requests\Api\V1\Account;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class SettleAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'amount' => 'required|numeric|min:0.01',
            'payment_type' => 'required|string|max:255',
        ];
    }

    public function attributes(): array
    {
        return [
            'amount' => 'Valor',
            'payment_type' => 'Forma de Pagamento',
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
