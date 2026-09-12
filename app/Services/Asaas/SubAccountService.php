<?php 

namespace App\Services\Asaas;

use App\Exceptions\ApiException;
use App\Jobs\GenerateAsaasPixKey;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Log;

class SubAccountService
{
    public function store(array $data) 
    {
        $authUser = Auth::user();

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
            'postalCode' => $authUser->address->cep
        ];

        try {

            $response = Http::timeout(60)
                ->withHeader('access_token', config('services.asaas.key'))
                ->post(config('services.asaas.url') . '/accounts', $payload)
                ->throw();

        } catch (\Exception $e) {
            throw new ApiException('Failed to sendo sub account to asaas: ' . $e->getMessage());
        }

        $account = $response->json();

        DB::transaction(function () use ($authUser, $account, $payload) {

            $subAccount = $authUser->subAccount()->create([
                'asaas_account_id' => $account['id'],
                'asaas_wallet_id' => $account['walletId'],
                'asaas_api_key' => $account['apiKey'],
                'asaas_access_token_id' => $account['accessToken']['id'],
                'asaas_access_token_api_key' => $account['accessToken']['apiKey'],
                'login_email' => $account['loginEmail'] ?? $payload['email'],
                'person_type' => $account['personType'] ?? null,
                'account_agency' => $account['accountNumber']['agency'] ?? null,
                'account_number' => $account['accountNumber']['account'] ?? null,
                'account_digit' => $account['accountNumber']['accountDigit'] ?? null,
                'commercial_info_expiration' => $account['commercialInfoExpiration'] ?? null,
            ]);

            GenerateAsaasPixKey::dispatch($subAccount)->afterCommit();
        });
    }

    // Create the function for help send request for Asaas
    private function request(string $method, array $payload, string $url) 
    {
        try { 
            //
        } catch (Exception $e) {
            throw new ApiException('Falha ao conectar na API do Asaas: ' . $e->getMessage());
        }
    }
}