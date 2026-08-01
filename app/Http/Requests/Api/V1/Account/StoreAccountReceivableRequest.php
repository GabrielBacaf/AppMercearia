<?php

namespace App\Http\Requests\Api\V1\Account;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class StoreAccountReceivableRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'due_date' => 'required|date',
            'client_id' => 'nullable|exists:clients,id',
            'installments' => 'nullable|integer|min:1',
            'payments' => 'nullable|array',
            'payments.*.amount' => 'required_with:payments|numeric|min:0.01',
            'payments.*.payment_type' => 'required_with:payments|string|max:255',
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => 'Título',
            'amount' => 'Valor',
            'due_date' => 'Data de Vencimento',
            'client_id' => 'Cliente',
            'installments' => 'Parcelas',
            'payments' => 'Pagamentos',
            'payments.*.amount' => 'Valor do Pagamento',
            'payments.*.payment_type' => 'Forma de Pagamento',
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
