<?php

namespace App\Models;

use App\Traits\HasUuidV7;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AsaasSubAccount extends Model
{
    use HasUuidV7;

    protected $fillable = [
        'user_id',
        'asaas_account_id',
        'asaas_wallet_id',
        'asaas_api_key',
        'asaas_access_token_id',
        'login_email',
        'person_type',
        'account_agency',
        'account_number',
        'account_digit',
        'status',
        'commercial_info_expiration',
        'asaas_pix_key',
        'asaas_pix_key_id',
        'asaas_access_token_api_key'
    ];

    protected $hidden = [
        'asaas_api_key',
    ];

    protected function casts(): array
    {
        return [
            'user_id' => 'string',
            'asaas_account_id' => 'string',
            'asaas_wallet_id' => 'string',
            'asaas_api_key' => 'encrypted',
            'asaas_access_token_id' => 'string',
            'login_email' => 'string',
            'person_type' => 'string',
            'account_agency' => 'string',
            'account_number' => 'string',
            'account_digit' => 'string',
            'status' => 'string',
            'commercial_info_expiration' => 'date',
            'asaas_pix_key' => 'string',
            'asaas_pix_key_id' => 'string',
            'asaas_access_token_api_key' => 'string'
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
