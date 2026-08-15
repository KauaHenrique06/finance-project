<?php

namespace App\Http\Resources\Transaction;

use App\Http\Resources\Auth\AuthResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GroupTransactionResource extends JsonResource
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
            'total_amount' => $this->total_amount,
            'owner' => new AuthResource($this->whenLoaded('owner')),
            'participants' => AuthResource::collection($this->whenLoaded('participant')),
        ];
    }
}
