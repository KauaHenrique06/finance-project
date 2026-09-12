<?php

namespace App\Services\Campaign;

use App\Enum\CampaignStatusEnum;
use App\Exceptions\ApiException;
use App\Models\Campaign;
use App\Services\Asaas\PaymentService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Str;

class CampaignService
{
    public function __construct(protected PaymentService $paymentService) {}

    public function index(array $data): LengthAwarePaginator
    {
        return Campaign::with('owner')->paginate($data['perPage'], ['*'], 'page', $data['page']);
    }

    public function store(array $data): Campaign
    {
        $authUser = Auth::user();
        $subAccount = $authUser->subAccount()->first();
        $campaignId = (string) Str::uuid7();

        if (!$subAccount || $subAccount->status !== 'approved')
        {
            throw new ApiException(
                "You must be have a valid sub account for create a campaing!"
            );
        }

        $qrCode = $this->paymentService->generatePaymentQrCode($data, $subAccount, $campaignId);

        $data = array_merge($data, [
            'owner_id' => $authUser->id,
            'asaas_qr_code_id' => $qrCode['id'],
            'asaas_qr_code_payload' => $qrCode['payload']
        ]);

        return DB::transaction(function () use ($data, $campaignId) {
            $campaign = new Campaign;
            $campaign->id = $campaignId;
            $campaign->fill($data)->save();

            return $campaign->load('owner');
        });
    }

    public function show(array $data): Campaign
    {
        return Campaign::with('owner')->findOrFail($data['id']);
    }

    // Adjust update for user cant change due date or update qr code too
    // Verify when user alter status manually
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
        $this->paymentService->deletePaymentQrCode($campaign);
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
            throw new ApiException("This campaign was {$campaign->status->value}");
        }

        if ($campaign->limit !== null && $data['contribute_amount'] + $campaign->total_collected > $campaign->limit) 
        {
            throw new ApiException('This amount is not permited!');
        }

    }
}
