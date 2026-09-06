<?php

namespace App\Services\Campaign;

use App\Enum\CampaignStatusEnum;
use App\Exceptions\ApiException;
use App\Models\Campaign;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class CampaignService
{
    public function index(array $data): LengthAwarePaginator
    {
        return Campaign::with('owner')->paginate($data['perPage'], ['*'], 'page', $data['page']);
    }

    public function store(array $data): Campaign
    {
        $authUserId = Auth::id();

        $data = array_merge($data, ['owner_id' => $authUserId]);

        return DB::transaction(function () use ($data) {
            $campaign = Campaign::create($data);
            $campaign->refresh();

            return $campaign->load('owner');
        });
    }

    public function show(array $data): Campaign
    {
        return Campaign::with('owner')->findOrFail($data['id']);
    }

    public function update(array $data): Campaign
    {
        $campaign = Campaign::findOrFail($data['id']);
        Gate::authorize('update', $campaign);

        unset($data['id']);

        return DB::transaction(function () use ($campaign, $data) {
            $campaign->update($data);
            $campaign->refresh();

            return $campaign->load('owner');
        });
    }

    public function destroy(array $data): void
    {
        $campaign = Campaign::findOrFail($data['id']);
        Gate::authorize('delete', $campaign);
        $campaign->delete();
    }

    public function contribute(array $data): void
    {
        $authUserId = Auth::id();
        $campaign = Campaign::findOrFail($data['id']);
        $this->canContribute($campaign, $data);

        DB::transaction(function () use ($campaign, $authUserId, $data) {
            $campaign->contributorCampaign()->create([
                'contributor_id' => $authUserId,
                'amount' => $data['contribute_amount']
            ]);
        });

    }

    private function canContribute(Campaign $campaign, array $data)
    {
        if ($campaign->status->value !== CampaignStatusEnum::IN_PROGRESS->value) 
        {
            throw new ApiException("This campaign isn't active!");
        }

        if ($campaign->limit !== null && $data['contribute_amount'] > $campaign->limit) 
        {
            throw new ApiException('This amount is not permited!');
        }

    }
}
