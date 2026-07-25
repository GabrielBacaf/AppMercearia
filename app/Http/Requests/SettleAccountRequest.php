<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SettleAccountRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'amount' => 'required|numeric|min:0.01',
            'payment_type' => 'required|string|max:255',
        ];
    }
}
