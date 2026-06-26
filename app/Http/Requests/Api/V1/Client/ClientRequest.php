<?php

namespace App\Http\Requests\Api\V1\Client;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;

class ClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'email', 'max:255', Rule::unique('clients', 'email')->ignore($this->client)],
            'phone' => ['nullable', 'string', 'max:20', Rule::unique('clients', 'phone')->ignore($this->client)],
            'address' => ['nullable', 'array'],
            'address.street' => ['nullable', 'string', 'max:255'],
            'address.number' => ['nullable', 'string', 'max:50'],
            'address.complement' => ['nullable', 'string', 'max:255'],
            'address.city' => ['nullable', 'string', 'max:100'],
            'address.state' => ['nullable', 'string', 'max:100'],
            'address.postal_code' => ['nullable', 'string', 'max:20'],
            'address.country' => ['nullable', 'string', 'max:100'],
            'address.latitude' => ['nullable', 'numeric'],
            'address.longitude' => ['nullable', 'numeric'],
        ];
    }


    public function attributes(): array
    {
        return [
            'name' => 'Nome do Cliente',
            'email' => 'Email do Cliente',
            'phone' => 'Telefone do Cliente',
            'address.street' => 'Rua',
            'address.number' => 'Número',
            'address.complement' => 'Complemento',
            'address.city' => 'Cidade',
            'address.state' => 'Estado',
            'address.postal_code' => 'Código Postal',
            'address.country' => 'País',
            'address.latitude' => 'Latitude',
            'address.longitude' => 'Longitude',
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
