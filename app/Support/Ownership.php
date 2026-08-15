<?php

namespace App\Support;

use App\Exceptions\ApiException;
use Illuminate\Support\Facades\Auth;

class Ownership
{
    public static function verify(?string $ownerId, string $message = 'You are not allowed to perform this action!'): void
    {
        if (Auth::id() !== $ownerId)
        {
            throw new ApiException($message, 403);
        }
    }
}
