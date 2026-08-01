<?php

namespace App\Http\Resources\V1\AccountReceivable;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AccountReceivableResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'amount' => $this->amount,
            'due_date' => $this->due_date?->format('Y-m-d'),
            'received_date' => $this->received_date?->format('Y-m-d'),
            'status' => $this->status,
            'client_id' => $this->client_id,
            'client' => new \App\Http\Resources\V1\Client\ClientResource($this->whenLoaded('client')),
            'receivable_id' => $this->receivable_id,
            'receivable_type' => $this->receivable_type,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
