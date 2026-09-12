<?php

namespace App\Models;

use App\Enum\CampaignStatusEnum;
use App\Policies\Campaign\CampaignPolicy;
use App\Traits\HasUuidV7;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[UsePolicy(CampaignPolicy::class)]
class Campaign extends Model
{
    use HasUuidV7, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'status',
        'limit',
        'owner_id',
        'due_date',
        'asaas_qr_code_id',
        'asaas_qr_code_payload',
    ];

    protected $appends = ['total_collected'];

    protected function casts(): array 
    {
        return [
            'title' => 'string',
            'description' => 'string',
            'status' => CampaignStatusEnum::class,
            'limit' => 'decimal:2',
            'owner_id' => 'string',
            'due_date' => 'date',
            'asaas_qr_code_id' => 'string',
            'asaas_qr_code_payload' => 'string',
        ];
    } 

    // Return total collected by group
    protected function totalCollected(): Attribute
    {
        $total = $this->contributorCampaign()->sum('amount');

        return new Attribute(
            get: fn () => (float)$total
        );
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function contributorCampaign(): HasMany
    {
        return $this->hasMany(CampaignContribution::class);
    }
}
