<?php

namespace App\Policies\Campaign;

use App\Models\Campaign;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CampaignPolicy
{
    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Campaign $campaign): Response
    {
        return $this->isOwner($user, $campaign)
            ? Response::allow()
            : Response::deny("Only owner can update this campaign!");
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Campaign $campaign): Response
    {
        return $this->isOwner($user, $campaign)
            ? Response::allow()
            : Response::deny("Only owner can delete this campaign!");
    }

    private function isOwner(User $user, Campaign $campaign): bool
    {
        return $user->id === $campaign->owner_id;
    }
}
