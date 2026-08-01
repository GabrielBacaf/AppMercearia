<?php

namespace App\Http\Requests\Api\V1\Financial;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class FinancialReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'month' => ['nullable', 'integer', 'min:1', 'max:12'],
            'year' => ['nullable', 'integer', 'min:2000', 'max:2100'],
        ];
    }

    public function attributes(): array
    {
        return [
            'month' => 'Mês',
            'year' => 'Ano',
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
