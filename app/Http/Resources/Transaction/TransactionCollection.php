<?php

namespace App\Http\Resources\Transaction;

use App\Http\Resources\Concerns\InteractsWithPagination;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class TransactionCollection extends ResourceCollection
{
    use InteractsWithPagination;

    public $collects = TransactionResource::class;

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
