<?php

namespace App\Http\Requests\Api\V1\Central;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class StoreTenantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => ['required', 'string', 'unique:tenants,id'],
            'domain' => ['required', 'string', 'unique:domains,domain'],
            'admin_name' => ['required', 'string', 'max:255'],
            'admin_email' => ['required', 'email'],
            'admin_password' => ['required', 'string', 'min:8'],
        ];
    }

    public function attributes(): array
    {
        return [
            'id' => 'ID da Loja',
            'domain' => 'Domínio',
            'admin_name' => 'Nome do Administrador',
            'admin_email' => 'E-mail do Administrador',
            'admin_password' => 'Senha do Administrador',
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
