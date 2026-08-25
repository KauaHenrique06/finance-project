<?php

namespace App\Http\Resources\Event;

use App\Http\Resources\Auth\AuthResource;
use App\Http\Resources\Group\GroupResource;
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
            'instance' => $this->whenLoaded('instance', fn () => [
                'id' => $this->instance->id,
                'name' => $this->instance->name,
                'number' => $this->instance->number,
                'status' => $this->instance->status,
            ]),
            'group' => GroupResource::collection($this->whenLoaded('group')),
        ];
    }
}
