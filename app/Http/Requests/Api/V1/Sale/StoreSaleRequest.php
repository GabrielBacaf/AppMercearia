<?php

namespace App\Http\Requests\Api\V1\Sale;

use App\Enums\PaymentStatusEnum;
use App\Enums\PaymentTypeEnum;
use App\Enums\SalePermissionEnum;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;


class StoreSaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can(SalePermissionEnum::STORE->value);
    }


    public function rules(): array
    {
        return [
            'discount' => ['sometimes', 'numeric', 'min:0' , 'max:10'],
            'delivery_price' => ['sometimes', 'numeric', 'min:0'],
            'user_id' => ['prohibited'],
            'updated_by' => ['prohibited'],
            'total_value' => ['prohibited'],
            'client_id' => ['sometimes', 'integer', 'exists:clients,id'],

            'products' => ['required', 'array', 'min:1'],
            'products.*.id' => ['sometimes', 'integer', Rule::exists('products', 'id')],
            'products.*.quantity' => ['required', 'integer', 'min:1'],

            'payments' => ['required', 'array', 'min:1'],
            'payments.*.payment_type' => ['required', 'string', Rule::in(PaymentTypeEnum::values())],
            'payments.*.payment_status' => ['required', 'string', Rule::in(PaymentStatusEnum::values())],
            'payments.*.value' => ['required', 'numeric', 'min:0.01', ],
        ];
    }

    public function attributes(): array
    {
        return [
            'discount' => 'DESCONTO',
            'delivery_price' => 'PREÇO DE ENTREGA',
            'user_id' => 'USUÁRIO',
            'updated_by' => 'USUARIO',
            'client_id' => 'CLIENTE',
            'products' => 'PRODUTO',
            'products.*.id' => 'ID DO PRODUTO',
            'products.*.quantity' => 'QUANTIDADE DO PRODUTO',
            'payments' => 'PAGAMENTOS',
            'payments.*.payment_type' => 'TIPO DE PAGAMENTO',
            'payments.*.payment_status' => 'STATUS DO PAGAMENTO',
            'payments.*.value' => 'VALOR DO PAGAMENTO',
            'total_value' => 'VALOR TOTAL',

        ];
    }

    public function validated($key = null, $default = null)
    {
        $validatedData = parent::validated($key, $default);

        $validatedData['user_id'] = $this->user()->id;

        return $validatedData;
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
