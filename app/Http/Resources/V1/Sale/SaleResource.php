<?php

namespace App\Http\Resources\V1\Supplier;

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
            'discount' => $this?->discount,
            'delivery_price' => $this?->delivery_price,
            'user_id' => $this?->user,
            'updated_by' => $this?->updated_by,
            'client_id' => $this?->client_id,
            'products' =>  $this->whenLoaded('products', function () {
                return $this->products->map(function ($product) {
                    return [
                'id' => $this?->id,
                'barcode' => $this?->barcode,
                'name' => $this?->name,
                'expiration_date' => $this?->productexpiration_date,
                'sale_value' => $this->products?->sale_value,
                    ]





            ]


            'expiration_date' => ['sometimes', 'date', 'after_or_equal:today'],
            'sale_value' => ['required', 'numeric', 'min:0'],
            'category' => ['required', 'string', Rule::in(CategoryEnum::values())],
            'stock_quantity' => ['prohibited'],
            'amount' => ['required', 'integer', 'min:0'],
            'purchase_id' => ['required', 'integer', 'exists:purchases,id'],
            'purchase_value' => ['required', 'numeric'],

        ];
    }
}
