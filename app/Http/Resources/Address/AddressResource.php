<?php

namespace App\Http\Resources\Address;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
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
            'cep' => $this->cep,
            'state' => $this->state,
            'number' => $this->number,
            'neighborhood' => $this->neighborhood,
            'street' => $this->street,
            'city' => $this->city,
        ];
    }
}
