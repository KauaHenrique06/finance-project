<?php

namespace App\Http\Resources\Whatsapp;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WhatsappInstanceResource extends JsonResource
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
            'number' => $this->number,
            'status' => $this->status,
            'qrcode_code' => $this->qrcode_code,
            'qrcode_base64' => $this->qrcode_base64,
            'owner_jid' => $this->owner_jid,
            'connected_at' => $this->connected_at,
        ];
    }
}
