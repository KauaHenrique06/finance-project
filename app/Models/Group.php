<?php

namespace App\Models;

use App\Policies\Group\GroupPolicy;
use App\Traits\HasUuidV7;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[UsePolicy(GroupPolicy::class)]
class Group extends Model
{
    use HasUuidV7, SoftDeletes;

    protected $table = 'groups';

    protected $fillable = [
        'title',
        'description',
        'total_amount',
        'owner_id',
        'event_id'
    ];

    protected function casts(): array
    {
        return [
            'title' => 'string',
            'description' => 'string',
            'owner_id' => 'string',
            'event_id' => 'string',
            'total_amount' => 'decimal:2'
        ];
    }

    // Return the query builder for continue mount
    #[Scope]
    protected function visibleTo(Builder $query, string $authUserId): void
    {
        $query->where(function ($q) use ($authUserId) {
            $q->where('owner_id', $authUserId)
            ->orWhereHas('participant', fn ($subQ) => $subQ->whereKey($authUserId));
        }); 
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transaction(): HasMany
    {
        return $this->hasMany(Transaction::class, 'group_id');
    }

    public function participant(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'group_user', 'group_id', 'participant_id')
            ->using(GroupUser::class)
            ->withPivot('amount', 'is_paid')
            ->withTimestamps();
    }

    public function message(): HasMany
    {
        return $this->hasMany(Message::class, 'group_id');
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
