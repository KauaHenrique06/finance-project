<?php

use App\Models\Group;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('notification.{id}', function ($user, $id) {
    return $user->id === $id;
});

Broadcast::channel('whatsapp-instance.{id}', function ($user, $id) {
    return $user->id === $id;
});

Broadcast::channel('message-sent.{group_id}', function ($user, $groupId) {
    return Group::visibleTo($user->id)->whereKey($groupId)->exists();
});