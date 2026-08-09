<?php

namespace App\Models;

use App\Traits\HasUuidV7;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Address extends Model
{
    use HasUuidV7;

    protected $fillable = [
        'cep',
        'state',
        'city',
        'neighborhood',
        'street',
        'number',
    ];

    protected function casts(): array
    {
        return [
            'cep' => 'string',
            'state' => 'string',
            'city' => 'string',
            'neighborhood' => 'string',
            'street' => 'string',
            'number' => 'integer',
        ];
    }

    public function user(): BelongsToMany 
    {
        return $this->belongsToMany(User::class);
    }
}
