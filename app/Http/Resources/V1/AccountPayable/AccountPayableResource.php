<?php

namespace App\Http\Resources\V1\AccountPayable;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AccountPayableResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'amount' => $this->amount,
            'due_date' => $this->due_date?->format('Y-m-d'),
            'paid_date' => $this->paid_date?->format('Y-m-d'),
            'status' => $this->status,
            'type' => $this->type,
            'payable_id' => $this->payable_id,
            'payable_type' => $this->payable_type,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
