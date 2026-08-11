<?php

namespace App\Models;

use App\Traits\HasUuidV7;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransactionUser extends Pivot
{
    use HasUuidV7, SoftDeletes;

    protected $fillable = [
        'participant_id',
        'transaction_id',
        'can_edit'
    ];

    public function casts(): array
    {
        return [
            'participant_id' => 'string',
            'group_id' => 'string',
            'can_edit' => 'boolean'
        ];
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function participant(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
