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

            //Documents
            'document_files'   => ['sometimes','array'],
            'document_files.*' => ['sometimes','file','mimes:pdf','max:5120'],

            //Array de labels
            'document_labels'    => ['sometimes','array','size:' ],
            'document_labels.*'  => ['sometimes','string','max:255'],
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

            //documents
            'document_files' => 'Documentos',
            'document_labels' => 'Labels',
        ];
    }


    public function messages(): array
    {
        return [
            'payments.*.payment_type.in' => 'O Tipo de Pagamento para o :positionº pagamento não é válido.',
            'payments.*.payment_status.in' => 'O Status do Pagamento para o :positionº pagamento não é válido.',
            'payments.*.value.required' => 'O Valor para o :positionº pagamento é obrigatório.',
            'payments.*.value.numeric' => 'O Valor para o :positionº pagamento deve ser um número.',
            'payments.*.value.min' => 'O Valor para o :positionº pagamento deve ser de no mínimo :min.',

            'document_files.*.mimes' => 'O arquivo do documento deve ser um arquivo do tipo: :values.',
            'document_files.*.max' => 'O arquivo do documento não deve ser maior que :max kilobytes.',
            'document_labels.*.max' => 'O label do documento não deve ser maior que :max caracteres.',
            'document_labels.*.size' => 'O número de labels dos documentos deve corresponder ao número de arquivos enviados.',
            'document_labels.*.string' => 'O label do documento deve ser uma string.',


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
