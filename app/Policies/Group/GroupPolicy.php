<?php

namespace App\Policies\Group;

use App\Models\Event;
use App\Models\Group;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class GroupPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Group $group): Response
    {
        return $this->isOwner($user, $group) || $this->isParticipant($user, $group)
            ? Response::allow()
            : Response::deny("You can't view this transaction group!");
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Event $event): Response
    {
        return $user->id === $event->owner_id
            ? Response::allow()
            : Response::deny("Only the owner of event can create groups");
    }

    /**
     * Determine whether the user can update the model.
     *
     * Provisório: só o owner edita o grupo.
     */
    public function update(User $user, Group $group): Response
    {
        return $this->isOwner($user, $group)
            ? Response::allow()
            : Response::deny("You can't update this transaction group!");
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Group $group): Response
    {
        return $this->isOwner($user, $group)
            ? Response::allow()
            : Response::deny("You can't delete this transaction group!");
    }

    /**
     * Determine whether the user can assign participants to the group.
     */
    public function assignParticipant(User $user, Group $group): Response
    {
        return $this->isOwner($user, $group)
            ? Response::allow()
            : Response::deny("You can't assign users to this transaction group!");
    }

    private function isOwner(User $user, Group $group): bool
    {
        return $user->id === $group->owner_id;
    }

    private function isParticipant(User $user, Group $group): bool
    {
        return $group->participant()
            ->where('participant_id', $user->id)
            ->exists();
    }
}
