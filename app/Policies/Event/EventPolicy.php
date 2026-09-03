<?php

namespace App\Policies\Event;

use App\Models\Event;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class EventPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Event $event): Response
    {
        return $this->isOwner($user, $event) || $this->isParticipant($user, $event) 
            ? Response::allow()
            : Response::deny("You can't view this event!");
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Event $event): Response
    {
        return $this->isOwner($user, $event)
            ? Response::allow()
            : Response::deny("You can't update this event!");
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Event $event): Response
    {
        return $this->isOwner($user, $event)
            ? Response::allow()
            : Response::deny("You can't delete this event!");
    }

    public function assignInstance(User $user, Event $event): Response
    {
        return $this->isOwner($user, $event)
            ? Response::allow()
            : Response::deny("You can't assign instance to this event!");
    }

    private function isOwner(User $user, Event $event): bool
    {
        return $user->id === $event->owner_id;
    }

    private function isParticipant(User $user, Event $event): bool
    {
        return $event->group()
            ->whereRelation('participant', 'participant_id', '=', $user->id)
            ->exists();
    }
}
