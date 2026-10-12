<?php

namespace App\Services\Dashboard;

use App\Enum\ContributionStatusEnum;
use App\Models\Campaign;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class CampaignDashboardService
{
    public function dashboard()
    {
        $campaigns = $this->getCampaigns(Auth::id());

        [$withLimit, $withoutLimit] = $campaigns->partition(
            fn (Campaign $campaign) => !is_null($campaign->limit)
        );

        return [
            'summary' => [
                'total_campaigns' => $campaigns->count(),
                'total_collected' => round($campaigns->sum('collected'), 2),
            ],
            'with_limit' => $this->calcWithLimit($withLimit),
            'without_limit' => $this->calcWithoutLimit($withoutLimit),
        ];
    }

    // Load auth user campaigns with sum and count of received contributions in a single query
    private function getCampaigns(string $authUserId): Collection
    {
        $received = fn ($query) => $query->where('status', ContributionStatusEnum::RECEIVED->value);

        return Campaign::filterByAuthUser($authUserId)
            ->withSum(['contributorCampaign as collected' => $received], 'amount')
            ->withCount(['contributorCampaign as contributions_count' => $received])
            ->latest()
            ->get()
            ->each(fn (Campaign $campaign) => $campaign->collected = (float) $campaign->collected);
    }

    // Campaigns with limit return the progress until reach the goal
    private function calcWithLimit(Collection $campaigns): array
    {
        return $campaigns->map(function (Campaign $campaign) {
            $limit = (float) $campaign->limit;

            return [
                ...$this->baseData($campaign),
                'limit' => $limit,
                'remaining' => round(max($limit - $campaign->collected, 0), 2),
                'percentage' => $limit > 0
                    ? round(($campaign->collected / $limit) * 100, 2)
                    : 0,
            ];
        })->values()->all();
    }

    // Campaigns without limit only return the total collected
    private function calcWithoutLimit(Collection $campaigns): array
    {
        return $campaigns->map(
            fn (Campaign $campaign) => $this->baseData($campaign)
        )->values()->all();
    }

    private function baseData(Campaign $campaign): array
    {
        return [
            'id' => $campaign->id,
            'title' => $campaign->title,
            'status' => $campaign->status,
            'due_date' => $campaign->due_date?->format('Y-m-d'),
            'collected' => round($campaign->collected, 2),
            'contributions_count' => $campaign->contributions_count,
        ];
    }
}
