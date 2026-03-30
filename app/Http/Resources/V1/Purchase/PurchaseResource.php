<?php

namespace App\Http\Resources\V1\Purchase;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'supplier_id' => $this->supplier_id,
            'invoice_id' => $this->invoice_id,
            'purchase_date' => $this->purchase_date,
            'count_value' => $this->count_value,
            'status' => $this->status,
            'user_id' => $this->user_id,

            //  Carregando os pagamentos corretamente
            'payments' => $this->whenLoaded('payments', function () {
                return $this->payments->map(function ($payment) {
                    return [
                        'id' => $payment->id,
                        'value' => $payment->value,
                        'payment_status' => $payment->payment_status,
                        'payable_id' => $payment->payable_id,
                        'payment_type' => $payment->payment_type,
                    ];
                });
            }),
        ];
    }
}
