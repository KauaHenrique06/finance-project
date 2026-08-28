<?php

namespace App\Http\Resources\Transaction;

use App\Http\Resources\Auth\AuthResource;
use App\Http\Resources\Group\GroupResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
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
            'group' => new GroupResource($this->whenLoaded('group')),
            'has_installment' => $this->has_installment,
            'amount' => $this->amount,
            'due_date' => $this->due_date,
            'installment_number' => $this->installment_number,
            'quantity_installment' => $this->quantity_installment,
            'status' => $this->status,
            'is_paid' => $this->is_paid,
            'payment_date' => $this->payment_date,
            'split_user_id' => $this->split_user_id,
            'payer' => new AuthResource($this->whenLoaded('payer')),
        ];
    }
}
