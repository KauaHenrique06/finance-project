<?php 

namespace App\Services\Asaas;

use App\Exceptions\ApiException;
use App\Jobs\VerifySubAccountDocuments;
use App\Models\AsaasSubAccount;
use App\Models\Campaign;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Log;

class SubAccountService
{
    public function store(array $data) 
    {
        $authUser = Auth::user();
        $url = (env('APP_URL') . 'api/webhook/asaas');

        $payload = [
            'name' => $authUser->name,
            'email' => $authUser->email,
            'cpfCnpj' => $authUser->cpf,
            'birthDate' => $authUser->birth_date,
            'mobilePhone' => $authUser->phone,
            'incomeValue' => $data['income_value'],
            'address' => $authUser->address->street,
            'addressNumber' => $authUser->address->number,
            'province' => $authUser->address->neighborhood,
            'postalCode' => $authUser->address->cep,
            'webhooks' => [
                [
                    'name' => 'General webhooks',
                    'url' => $url,
                    'email' => $authUser->email,
                    'enabled' => true,
                    'interrupted' => false,
                    'apiVersion' => 3,
                    'authToken' => config('services.asaas.webhook_token'),
                    'sendType' => 'NON_SEQUENTIALLY',
                    'events' => [
                        'PAYMENT_RECEIVED',
                        'ACCOUNT_STATUS_GENERAL_APPROVAL_AWAITING_APPROVAL',
                        'ACCOUNT_STATUS_GENERAL_APPROVAL_APPROVED',
                        'ACCOUNT_STATUS_GENERAL_APPROVAL_PENDING',
                        'ACCOUNT_STATUS_GENERAL_APPROVAL_REJECTED',
                    ]
                ]
            ]
        ];

        try {

            $response = Http::timeout(60)
                ->withHeader('access_token', config('services.asaas.key'))
                ->post(config('services.asaas.url') . '/accounts', $payload)
                ->throw();

        } catch (Exception $e) {
            throw new ApiException('Failed to send sub account to asaas: ' . $e->getMessage());
        }

        $account = $response->json();

        return DB::transaction(function () use ($authUser, $account) {

            $subAccount = $authUser->subAccount()->create([
                'asaas_account_id' => $account['id'],
                'asaas_wallet_id' => $account['walletId'],
                'asaas_api_key' => $account['apiKey'],
                'asaas_access_token_id' => $account['accessToken']['id'],
                'asaas_access_token_api_key' => $account['accessToken']['apiKey'],
                'login_email' => $account['loginEmail'],
                'person_type' => $account['personType'] ?? null,
                'account_agency' => $account['accountNumber']['agency'] ?? null,
                'account_number' => $account['accountNumber']['account'] ?? null,
                'account_digit' => $account['accountNumber']['accountDigit'] ?? null,
                'commercial_info_expiration' => $account['commercialInfoExpiration'] ?? null,
            ]);

            VerifySubAccountDocuments::dispatch($subAccount)->afterCommit()->delay(now()->addSeconds(20));
        });
    }

    /**
     * Summary of destroy
     * 
     * Register a valid IP for use this feature
     * on website https://www.asaas.com/ in security page
     */
    public function destroy(): void  
    {
        $authUserId = Auth::id();
        $subAccount = AsaasSubAccount::where('user_id', $authUserId)->firstOrFail();
        $campaigns = Campaign::where('owner_id', $subAccount->user_id)->get();

        try {

            Http::withHeader('access_token', config('services.asaas.key'))
                ->withUrlParameters(['id' => $subAccount->asaas_account_id])
                ->delete(config('services.asaas.url') . '/accounts/{id}')
                ->throw();
        } catch (Exception $e) {
            throw new ApiException('Failed to delete sub account!' . $e->getMessage());
        }

        $subAccount->delete();
        $campaigns->map(fn ($campaign) => $campaign->delete());
    }
}