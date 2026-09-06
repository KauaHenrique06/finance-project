<?php

namespace App\Models;

use App\Enum\CampaignStatusEnum;
use App\Policies\Campaign\CampaignPolicy;
use App\Traits\HasUuidV7;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[UsePolicy(CampaignPolicy::class)]
class Campaign extends Model
{
    use HasUuidV7, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'status',
        'total_collected',
        'limit',
        'owner_id',
        'due_date'
    ];

    protected function casts(): array 
    {
        return [
            'title' => 'string',
            'description' => 'string',
            'status' => CampaignStatusEnum::class,
            'total_collected' => 'decimal:2',
            'limit' => 'decimal:2',
            'owner_id' => 'string',
            'due_date' => 'date'
        ];
    } 

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}
