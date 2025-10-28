<?php

namespace App\Http\Resources\V1\Client;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class ClientResource extends JsonResource
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
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => [
                'street' => $this->address->street,
                'number' => $this->address->number,
                'complement' => $this->address->complement,
                'city' => $this->address->city,
                'state' => $this->address->state,
                'postal_code' => $this->address->postal_code,
                'country' => $this->address->country,
                'latitude' => $this->address->latitude,
                'longitude' => $this->address->longitude,
            ],
        ];
    }
}
