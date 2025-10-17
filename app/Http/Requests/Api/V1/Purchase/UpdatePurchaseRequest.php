<?php

namespace App\Http\Requests\Api\V1\Purchase;

use App\Enums\CategoryEnum;
use App\Enums\PaymentStatusEnum;
use App\Enums\PaymentTypeEnum;
use App\Enums\PurchasePermissionEnum;
use App\Enums\StatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;


class UpdatePurchaseRequest extends FormRequest
{

    public function authorize(): bool
    {
        return $this->user()?->can(PurchasePermissionEnum::UPDATE->value);
    }

    public function rules(): array
    {
        $purchaseId = $this->route('purchase')->id;

        return [
            //purchase
            'title' => ['required', 'string', 'max:50'],
            'description' => ['sometimes', 'string', 'max:255'],
            'purchase_date' => ['required', 'date', 'before_or_equal:today'],
            'supplier_id' => ['sometimes', 'integer', Rule::exists('suppliers', 'id')],
            'invoice_id' => ['sometimes', 'integer', Rule::exists('invoices', 'id')],
            'status' => ['prohibited'],
            'count_value' => ['prohibited'],
            'user_id' => ['prohibited'],
            'updated_by' => ['prohibited'],

            //payments
            'payments' => ['required', 'array', 'min:1'],
            'payments.*.id' => [
                'sometimes',
                'integer',

                Rule::exists('payments', 'id')->where('purchase_id', $purchaseId),
            ],
            'payments.*.payment_type' => ['required', 'string', Rule::in(PaymentTypeEnum::values())],
            'payments.*.payment_status' => ['required', 'string', Rule::in(PaymentStatusEnum::values())],
            'payments.*.value' => ['required', 'numeric', 'min:0.01'],
        ];
    }

    public function attributes(): array
    {
        return [
            //purchase
            'title' => 'Titulo',
            'description' => 'Descrição da Compra',
            'purchase_date' => 'Data de Compra',
            'count_value' => 'Contagem do valor da Compra',
            'status' => 'Status',
            'supplier_id' => 'Fornecedor',
            'invoice_id' => 'Nota',
            'user_id' => 'Usuario',
            'updated_by' => 'Atualizado por',

            //payments
            'payments' => 'Pagamentos',
            'payments.*.id' => 'ID do Pagamento',
            'payments.*.value' => 'Valor do Pagamento',
            'payments.*.payment_type' => 'Tipo do Pagamento',
            'payments.*.payment_status' => 'Status do Pagamento',
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
