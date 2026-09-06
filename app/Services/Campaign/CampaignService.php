<?php

namespace App\Services\Campaign;

use App\Models\Campaign;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CampaignService
{
    public function index(array $data)
    {
        return Campaign::with('owner')->paginate($data['perPage'], ['*'], 'page', $data['page']);
    }

    public function store(array $data)
    {
        $authUserId = Auth::id();

        $data = array_merge($data, ['owner_id' => $authUserId]);

        return DB::transaction(function () use ($data) {
            $campaign = Campaign::create($data);
            $campaign->refresh();

            return $campaign->load('owner');
        });
    }

    public function show(array $data)
    {
        return Campaign::with('owner')->findOrFail($data['id']);
    }

    public function update(array $data)
    {
        $campaign = Campaign::findOrFail($data['id']);

        unset($data['id']);

        return DB::transaction(function () use ($campaign, $data) {
            $campaign->update($data);
            $campaign->refresh();

            return $campaign->load('owner');
        });
    }

    public function destroy(array $data)
    {
        $campaign = Campaign::findOrFail($data['id']);

        $campaign->delete();
    }
}
