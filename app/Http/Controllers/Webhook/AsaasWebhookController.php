<?php

namespace App\Http\Controllers\Webhook;

use App\Enum\AsaasSubAccountStatusEnum;
use App\Enum\ContributionStatusEnum;
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
            'ACCOUNT_STATUS_GENERAL_APPROVAL_APPROVED' => $this->handleVerifySubAccountStatus($request),
            'ACCOUNT_STATUS_GENERAL_APPROVAL_REJECTED' => $this->handleVerifySubAccountStatus($request),
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

    protected function handleVerifySubAccountStatus($request)
    {
        $status = $request['accountStatus']['general'];
        $subAccount = AsaasSubAccount::where(
            'asaas_account_id', $request['account']['id']
        )->firstOrFail();

        match ($status) 
        {
            'APPROVED' => $this->updateAccountStatus(AsaasSubAccountStatusEnum::APPROVED, $subAccount),
            'REJECTED' => $this->updateAccountStatus(AsaasSubAccountStatusEnum::REJECTED, $subAccount),
            default => Log::debug('Account status ignored: ' . $status)
        };
    }

    private function updateAccountStatus(AsaasSubAccountStatusEnum $status, AsaasSubAccount $subAccount)
    {
        DB::transaction(fn () => $subAccount->update(['status' => $status]));

        if ($status === AsaasSubAccountStatusEnum::APPROVED) 
        {
            GenerateAsaasPixKey::dispatch($subAccount);
        }
    }
}
