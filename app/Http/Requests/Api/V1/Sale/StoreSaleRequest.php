<?php

namespace App\Http\Requests\Api\V1\Role;

use App\Enums\SalePermissionEnum;
use Illuminate\Foundation\Http\FormRequest;


class StoreSaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can(SalePermissionEnum::STORE->value);
    }

    public function rules(): array
    {
        return [
            'discount' => ['sometimes', 'decimal'],
            'delivery_price' => ['sometimes', 'decimal'],
            'user_id' => ['prohibited'],
            'updated_by' => ['prohibited'],
            'client_id' => ['sometimes', 'integer', 'exists:clients,id'],
            'products' => ['required', 'array', 'min:1'],
        ];

    }

    public function attributes(): array
    {
        return [

        ];
    }


}


