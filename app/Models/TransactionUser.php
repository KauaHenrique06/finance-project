<?php

namespace App\Models;

use App\Traits\HasUuidV7;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransactionUser extends Pivot
{
    use HasUuidV7, SoftDeletes;

    protected $table = 'transaction_user';

    protected $fillable = [
        'participant_id',
        'group_id',
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

    public function groupTransaction(): BelongsTo
    {
        return $this->belongsTo(GroupTransaction::class, 'group_id');
    }

    public function participant(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
