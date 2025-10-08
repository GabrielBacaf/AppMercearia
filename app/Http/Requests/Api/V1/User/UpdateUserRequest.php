<?php

namespace App\Http\Requests\Api\V1\User;

use App\Enums\UserPermissionEnum;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class UpdateUserRequest extends FormRequest
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

        // Pega o ID do usuário que está na rota (ex: /api/users/5)
        $userId = $this->route('user')->id;

        return [
            'name' => ['sometimes', 'string', 'max:100'],
            'login' => ['sometimes', 'string', 'max:50', Rule::unique('users', 'login')->ignore($userId)],
            'email' => ['nullable', 'email', 'max:100', Rule::unique('users', 'email')->ignore($userId)],
            'password' => ['sometimes', 'string', 'min:6', 'confirmed'],
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
