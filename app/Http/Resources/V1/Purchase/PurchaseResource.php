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
            'supplier_id' => $this->supplier_id,
            'invoice_id' => $this->invoice_id,
            'purchase_date' => $this->purchase_date,
            'amount' => $this->amount,
            'status' => $this->status,
            'user_id' => $this->user_id,
        ];
    }
}
