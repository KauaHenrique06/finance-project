<?php

namespace App\Models;

use App\Enum\TransactionStatusEnum;
use App\Traits\HasUuidV7;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasUuidV7, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'has_installment',
        'quantity_installment',
        'owner_id',
        'status',
        'is_paid',
        'installment_number',
        'due_date',
        'group_id',
        'payment_date',
        'payer_id',
    ];

    protected function casts(): array
    {
        return [
            'title' => 'string',
            'description' => 'string',
            'has_installment' => 'boolean',
            'quantity_installment' => 'integer',
            'owner_id' => 'string',
            'status' => TransactionStatusEnum::class,
            'is_paid' => 'boolean',
            'installment_number' => 'integer',
            'due_date' => 'date',
            'group_id' => 'string',
            'payment_date' => 'date',
            'payer_id' => 'string',
        ];
    }

    public function payer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'payer_id');
    }

    public function groupTransaction(): BelongsTo
    {
        return $this->belongsTo(GroupTransaction::class, 'group_id');
    }
}
