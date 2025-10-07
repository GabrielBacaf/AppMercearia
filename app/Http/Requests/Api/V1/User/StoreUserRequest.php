<?php

namespace App\Http\Requests\Api\V1\User;

use App\Enums\UserPermissionEnum;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'login' => ['required', 'string', 'max:50', 'unique:users,login'],
            'email' => ['sometimes', 'email', 'max:100', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'status' => ['sometimes', 'boolean'],

        ];
    }
    public function attributes(): array
    {
        return [
            'name' => 'Nome',
            'login' => 'Login',
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
            'errors'  => $validator->errors()
        ], 422));
    }
}
