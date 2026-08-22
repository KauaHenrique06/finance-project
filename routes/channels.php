<?php

use App\Models\GroupTransaction;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('notification.{id}', function ($user, $id) {
    return $user->id === $id;
});

Broadcast::channel('whatsapp-instance.{id}', function ($user, $id) {
    return $user->id === $id;
});

Broadcast::channel('message-sent.{group_id}', function ($user, $groupId) {
    return GroupTransaction::visibleTo($user->id)->whereKey($groupId)->exists();
});