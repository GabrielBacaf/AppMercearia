<?php

namespace App\Http\Requests\Api\V1\Central;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class UpdateTenantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('tenant');

        return [
            'domain' => ['sometimes', 'string', 'unique:domains,domain,' . $id . ',tenant_id'],
            'data'   => ['sometimes', 'array']
        ];
    }

    public function attributes(): array
    {
        return [
            'domain' => 'Domínio',
            'data' => 'Dados Adicionais',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Erro de validação',
            'errors'  => $validator->errors()
        ], 422));
    }
}
