<?php

namespace App\Http\Resources\V1\Supplier;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SupplierResource extends JsonResource
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
            'fantasy_name' => $this->fantasy_name,
            'legal_name' => $this->legal_name,
            'cnpj' => $this->cnpj,
        ];
    }
}
