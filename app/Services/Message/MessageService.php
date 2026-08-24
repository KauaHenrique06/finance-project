<?php

namespace App\Services\Message;

use App\Events\MessageSentEvent;
use App\Models\Group;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class MessageService
{
    public function store(array $data)
    {
        $data = array_merge($data, [
            'user_id' => Auth::id(), 
            'group_id' => $data['id']
        ]);

        $group = Group::findOrFail($data['id']);

        Gate::authorize('view', $group);

        $message = DB::transaction(fn () => Message::create($data));

        // Send only to others integrants
        broadcast(new MessageSentEvent($message))->toOthers();

        return $message->load('user');
    }

    public function index(array $data)
    {
        $group = Group::findOrFail($data['id']);

        Gate::authorize('view', $group);

        return Message::with('user')
            ->where('group_id', $data['id'])
            ->latest()
            ->paginate($data['perPage'], ['*'], 'page', $data['page']);
    }
}