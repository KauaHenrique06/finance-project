<?php

namespace App\Models;

use App\Traits\HasUuidV7;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class GroupUser extends Pivot
{
    use HasUuidV7, SoftDeletes;

    protected $table = 'group_user';

    protected $fillable = [
        'participant_id',
        'group_id',
        'amount',
        'is_paid'
    ];

    public function casts(): array
    {
        return [
            'participant_id' => 'string',
            'group_id' => 'string',
            'amount' => 'decimal:2',
            'is_paid' => 'boolean'
        ];
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    public function participant(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
