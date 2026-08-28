<?php

namespace App\Models;

use App\Enum\TransactionStatusEnum;
use App\Traits\HasUuidV7;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasUuidV7, SoftDeletes;

    protected $fillable = [
        'has_installment',
        'quantity_installment',
        'status',
        'is_paid',
        'installment_number',
        'due_date',
        'group_id',
        'payment_date',
        'payer_id',
        'amount',
        'user_id'
    ];

    protected function casts(): array
    {
        return [
            'has_installment' => 'boolean',
            'quantity_installment' => 'integer',
            'status' => TransactionStatusEnum::class,
            'is_paid' => 'boolean',
            'installment_number' => 'integer',
            'due_date' => 'date',
            'group_id' => 'string',
            'payment_date' => 'date',
            'payer_id' => 'string',
            'amount' => 'decimal:2',
            'user_id' => 'string',
        ];
    }

    #[Scope]
    protected function visibleTo(Builder $query, string $authUserId): void
    {
        $query->whereHas('group', fn ($q) => $q->visibleTo($authUserId));
    }

    public function payer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'payer_id');
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
