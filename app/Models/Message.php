<?php

namespace App\Models;

use App\Traits\HasUuidV7;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Message extends Model
{
    use HasUuidV7;

    protected $fillable = [
        'body',
        'group_id',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'body' => 'string',
            'group_id' => 'string',
            'user_id' => 'string',
        ];
    } 

    public function group(): BelongsTo
    {
        return $this->belongsTo(GroupTransaction::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    } 
}
