<?php

namespace App\Policies\Transaction;

use App\Models\GroupTransaction;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class GroupTransactionPolicy
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
    public function view(User $user, GroupTransaction $groupTransaction): Response
    {
        return $this->isOwner($user, $groupTransaction) || $this->isParticipant($user, $groupTransaction)
            ? Response::allow()
            : Response::deny("You can't view this transaction group!");
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     *
     * Provisório: o owner sempre pode; participante só com can_edit na pivot.
     */
    public function update(User $user, GroupTransaction $groupTransaction): Response
    {
        if ($this->isOwner($user, $groupTransaction))
        {
            return Response::allow();
        }

        $canEdit = $groupTransaction->participant()
            ->where('participant_id', $user->id)
            ->wherePivot('can_edit', true)
            ->exists();

        return $canEdit
            ? Response::allow()
            : Response::deny("You can't update this transaction group!");
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, GroupTransaction $groupTransaction): Response
    {
        return $this->isOwner($user, $groupTransaction) || $this->isParticipant($user, $groupTransaction)
            ? Response::allow()
            : Response::deny("You can't delete this transaction group!");
    }

    /**
     * Determine whether the user can assign participants to the group.
     */
    public function assignParticipant(User $user, GroupTransaction $groupTransaction): Response
    {
        return $this->isOwner($user, $groupTransaction)
            ? Response::allow()
            : Response::deny("You can't assign users to this transaction group!");
    }

    /**
     * Determine whether the user can attach a WhatsApp instance to the group.
     */
    public function assignInstance(User $user, GroupTransaction $groupTransaction): Response
    {
        return $this->isOwner($user, $groupTransaction)
            ? Response::allow()
            : Response::deny("You can't assign instance to this transaction group!");
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, GroupTransaction $groupTransaction): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, GroupTransaction $groupTransaction): bool
    {
        return false;
    }

    private function isOwner(User $user, GroupTransaction $groupTransaction): bool
    {
        return $user->id === $groupTransaction->owner_id;
    }

    private function isParticipant(User $user, GroupTransaction $groupTransaction): bool
    {
        return $groupTransaction->participant()
            ->where('participant_id', $user->id)
            ->exists();
    }
}
