<?php

namespace App\Models;

use App\Enum\ContributionStatusEnum;
use App\Traits\HasUuidV7;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignContribution extends Model
{
    use HasUuidV7;

    protected $fillable = [
        'campaign_id',
        'asaas_payment_id',
        'status',
        'amount',
        'paid_at'
    ];

    protected function casts(): array
    {
        return [
            'campaign_id' => 'string',
            'status' => ContributionStatusEnum::class,
            'amount' => 'decimal:2',
            'paid_at' => 'datetime'
        ];
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }
}
