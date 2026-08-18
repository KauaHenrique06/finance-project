<?php

namespace App\Http\Resources\Whatsapp;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class WhatsappInstanceCollection extends ResourceCollection
{
    public $collects = WhatsappInstanceResource::class;

    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection,
            'pagination' => [
                'current_page' => $this->currentPage(),
                'last_page' => $this->lastPage(),
                'per_page' => $this->perPage(),
                'total' => $this->total(),
            ]
        ];
    }
}
