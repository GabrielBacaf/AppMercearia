<?php

namespace App\Http\Requests\Api\V1\Purchase;

use App\Enums\CategoryEnum;
use App\Enums\PurchasePermissionEnum;
use App\Enums\StatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;

class StorePurchaseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can(PurchasePermissionEnum::STORE->value);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:50'],
            'description' => ['sometimes', 'string', 'max:255'],
            'purchase_date' => ['required', 'date', 'before_or_equal:today'],
            'status' => ['prohibited'],
            'value' => ['required', 'numeric'],
            'count_value' => ['prohibited'],
            'supplier_id' => ['nullable', 'integer', 'exists:suppliers,id'],
            'invoice_id' => ['nullable', 'integer', 'exists:invoices,id'],

        ];
    }

    public function attributes(): array
    {
        return [
            'title' => 'Titulo',
            'description' => 'Descrição da Compra',
            'purchase_date' => 'Data de Compra',
            'count_value' => 'Contagem do valor da Compra',
            'value' => 'Quantia da Compra',
            'status' => 'Status',
            'supplier_id' => 'Fornecedor',
            'invoice_id' => 'Nota',
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
