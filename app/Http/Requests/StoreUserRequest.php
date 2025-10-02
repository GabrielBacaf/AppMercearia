<?php

namespace App\Http\Requests;

use App\Enums\UserPermissionEnum;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->can(UserPermissionEnum::CREATE->value);
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
            'password' => ['required', 'string', 'min:4', 'max:12', 'confirmed'],
            'is_admin' => ['sometimes', 'boolean'],
        ];
    }
}
