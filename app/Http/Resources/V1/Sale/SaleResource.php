<?php

namespace App\Http\Resources\V1\Sale;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SaleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'discount'       => $this->discount,
            'delivery_price' => $this->delivery_price,
            'user_id'        => $this->user_id,
            'updated_by'     => $this->updated_by,
            'client_id'      => $this->client_id,
            'total_value'    => $this->total_value,

            'products' => $this->whenLoaded('products', function () {
                return $this->products->map(function ($product) {
                    return [
                        'id'              => $product->id,
                        'barcode'         => $product->barcode,
                        'name'            => $product->name,
                        'expiration_date' => $product->expiration_date,
                        'current_stock'   => $product->stock_quantity,
                        'quantity_sold'   => $product->pivot->amount,
                        'sale_value'      => $product->pivot->sale_value,
                    ];
                });
            }),

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
