<?php

namespace App\Http\Resources\Event;

use App\Http\Resources\Auth\AuthResource;
use App\Http\Resources\Transaction\GroupTransactionResource;
use App\Http\Resources\Whatsapp\WhatsappInstanceResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
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
            'owner' => new AuthResource($this->whenLoaded('owner')),
            'instance' => new WhatsappInstanceResource($this->whenLoaded('instance')),
            'group' => GroupTransactionResource::collection($this->whenLoaded('group')),
        ];
    }
}
