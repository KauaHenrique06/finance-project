<?php

namespace App\Http\Resources\Whatsapp;

use App\Http\Resources\Concerns\InteractsWithPagination;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class WhatsappInstanceCollection extends ResourceCollection
{
    use InteractsWithPagination;

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
            'pagination' => $this->paginationMeta(),
        ];
    }
}
