<?php

namespace App\Http\Resources\V1\Product;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'barcode' => $this->barcode,
            'name' => $this->name,
            'expiration_date' => $this->expiration_date,
            'sale_value' => $this->sale_value,
            'category' => $this->category,
            'stock_quantity' => $this->stock_quantity,

        ];
    }
}
