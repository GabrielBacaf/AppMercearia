<?php

namespace App\Http\Requests;

use App\Enums\UserPermissionEnum;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', User::class);
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
            'login' => ['sometimes', 'string', 'max:50', Rule::unique('users')->ignore($userId)],
            'email' => ['sometimes', 'email', 'max:100', 'nullable', Rule::unique('users')->ignore($userId)],
            'password' => ['sometimes', 'string', 'min:4', 'max:12', 'confirmed'],
            'status' => ['sometimes', 'boolean'],
        ];
    }
}
