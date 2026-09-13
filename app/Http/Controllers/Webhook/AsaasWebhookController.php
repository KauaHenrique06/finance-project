<?php

namespace App\Http\Controllers\Webhook;

use App\Enum\ContributionStatusEnum;
use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Models\AsaasSubAccount;
use App\Models\Campaign;
use App\Models\CampaignContribution;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AsaasWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $event = $request->input('event');
        $subAccount = AsaasSubAccount::where('asaas_account_id', $request->input('account.id'))
            ->firstOrFail();

        if ($subAccount->status !== 'approved')
        {
            throw new ApiException('The sub account must be approved for create a payment!');
        }

        match ($event)
        {
            'PAYMENT_RECEIVED' => $this->handlePaymentCreated($request),
            default => Log::debug('Event ignored: ' . $event)
        };
    }

    protected function handlePaymentCreated($request): void
    {
        $campaign = Campaign::findOrFail($request['payment']['externalReference']);

        DB::transaction(function () use ($campaign, $request) {
            CampaignContribution::firstOrCreate(
                ['asaas_payment_id' => $request->input('payment.id')],
                [
                    'campaign_id' => $campaign->id,
                    'status' => ContributionStatusEnum::RECEIVED,
                    'amount' => $request->input('payment.value'),
                    'paid_at' => $request->input('payment.confirmedDate'),
                ]
            );
        });
    }

    protected function handleSubAccountApproved()
    {

    }
}
