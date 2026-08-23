<?php

namespace App\Models;

use App\Traits\HasUuidV7;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use HasUuidV7, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'owner_id',
        'instance_id',
    ];

    protected function casts(): array 
    {
        return [
            'title' => 'string',
            'description' => 'string',
            'owner_id' => 'string',
            'instance_id' => 'string',
        ];
    }

    public function instance(): BelongsTo
    {
        return $this->belongsTo(WhatsappInstance::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function group(): HasMany
    {
        return $this->hasMany(GroupTransaction::class);
    }
}
