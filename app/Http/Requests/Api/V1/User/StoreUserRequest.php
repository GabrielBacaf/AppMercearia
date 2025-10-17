<?php

namespace App\Http\Requests\Api\V1\User;

use App\Enums\UserPermissionEnum;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;

class StoreUserRequest extends FormRequest
{
  
    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'login' => ['required', 'string', 'max:50', Rule::unique('users', 'login')],
            'email' => ['sometimes', 'email', 'max:100', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => [Rule::exists('roles', 'name')],
            'status' => ['sometimes', 'boolean'],

        ];
    }
    public function attributes(): array
    {
        return [
            'name' => 'Nome',
            'login' => 'Login',
            'roles' => 'PERFIS',
            'email' => 'E-mail',
            'password' => 'Senha',
            'status' => 'Status',
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
