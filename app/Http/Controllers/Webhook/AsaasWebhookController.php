<?php

namespace App\Http\Controllers\Webhook;

use App\Enum\AsaasSubAccountStatusEnum;
use App\Enum\ContributionStatusEnum;
use App\Exceptions\ApiException;
use App\Helper\RequestHelper;
use App\Http\Controllers\Controller;
use App\Jobs\GenerateAsaasPixKey;
use App\Models\AsaasSubAccount;
use App\Models\Campaign;
use App\Models\CampaignContribution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AsaasWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $event = $request->input('event');
        Log::info('webhook: ' . $request);

        match ($event)
        {
            'PAYMENT_RECEIVED' => $this->handlePaymentCreated($request),
            'ACCOUNT_STATUS_GENERAL_APPROVAL_AWAITING_APPROVAL' => $this->handleSubAccountApproved($request),
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

    protected function handleSubAccountApproved($request)
    {
        $status = $request['accountStatus']['general'];
        $subAccount = AsaasSubAccount::where(
            'asaas_account_id', $request['account']['id']
        )->firstOrFail();

        $statusForUpdate = match ($status) 
        {
            'APPROVED' => AsaasSubAccountStatusEnum::APPROVED->value
        };

        DB::transaction(fn () => $subAccount->update(['status' => $statusForUpdate]));

        if ($subAccount->status === AsaasSubAccountStatusEnum::APPROVED)
        {
            GenerateAsaasPixKey::dispatch($subAccount);
        }
    }
}
