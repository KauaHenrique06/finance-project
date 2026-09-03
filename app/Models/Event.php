<?php

namespace App\Models;

use App\Policies\Event\EventPolicy;
use App\Traits\HasUuidV7;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[UsePolicy(EventPolicy::class)]
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

    // Return the query builder for continue mount
    #[Scope]
    protected function visibleTo(Builder $query, string $authUserId): void
    {
        $query->where(function ($q) use ($authUserId) {
            $q->where('owner_id', $authUserId)
            ->orWhereHas('group.participant', fn ($subQ) => $subQ->whereKey($authUserId));
        });
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
        return $this->hasMany(Group::class);
    }
}
