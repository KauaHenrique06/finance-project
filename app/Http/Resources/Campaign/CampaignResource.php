<?php

namespace App\Http\Resources\Campaign;

use App\Http\Resources\Auth\AuthResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CampaignResource extends JsonResource
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
            'status' => $this->status,
            'total_collected' => $this->total_collected,
            'limit' => $this->limit,
            'due_date' => $this->due_date,
            'total_collect' => $this->total_collect,
            'owner' => new AuthResource($this->whenLoaded('owner')),
        ];
    }
}
