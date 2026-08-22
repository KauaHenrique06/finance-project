<?php

namespace App\Models;

use App\Policies\Transaction\GroupTransactionPolicy;
use App\Traits\HasUuidV7;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[UsePolicy(GroupTransactionPolicy::class)]
class GroupTransaction extends Model
{
    use HasUuidV7, SoftDeletes;

    protected $table = 'group_transaction';

    protected $fillable = [
        'title',
        'description',
        'owner_id',
        'instance_id',
        'total_amount'
    ];

    protected function casts(): array
    {
        return [
            'title' => 'string',
            'description' => 'string',
            'owner_id' => 'string',
            'instance_id' => 'string',
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
        return $this->belongsToMany(User::class, 'transaction_user', 'group_id', 'participant_id')
            ->using(TransactionUser::class)
            ->withPivot('can_edit')
            ->withTimestamps();
    }

    public function whatsappInstance(): BelongsTo
    {
        return $this->belongsTo(WhatsappInstance::class, 'instance_id');
    }

    public function message(): HasMany
    {
        return $this->hasMany(Message::class, 'group_id');
    }
}
