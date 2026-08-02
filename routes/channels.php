<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('notification.{id}', function ($user, $id) {
    return $user->id === $id;
});
