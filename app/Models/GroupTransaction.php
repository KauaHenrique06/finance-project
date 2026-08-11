<?php

namespace App\Models;

use App\Traits\HasUuidV7;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class GroupTransaction extends Model
{
    use HasUuidV7, SoftDeletes;

    protected $table = 'group_transaction';

    protected $fillable = [
        'title',
        'description',
        'owner_id',
    ];

    protected function casts(): array
    {
        return [
            'title' => 'string',
            'description' => 'string',
            'owner_id' => 'string',
        ];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transaction(): HasMany 
    {
        return $this->hasMany(Transaction::class, 'group_id');
    }
}
