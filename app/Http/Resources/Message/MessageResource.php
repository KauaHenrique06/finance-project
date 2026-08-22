<?php

namespace App\Http\Resources\Message;

use App\Http\Resources\Auth\AuthResource;
use App\Http\Resources\Transaction\GroupTransactionResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
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
            'user' => new AuthResource($this->whenLoaded('user')),
            'group' => new GroupTransactionResource($this->whenLoaded('group')),
            'body' => $this->body,
        ];
    }
}
