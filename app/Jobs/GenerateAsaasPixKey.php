<?php

namespace App\Jobs;

use App\Exceptions\ApiException;
use App\Models\AsaasSubAccount;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Log;

class GenerateAsaasPixKey implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(protected AsaasSubAccount $subAccount) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info($this->subAccount->asaas_access_token_api_key);
        try {

            $response = Http::timeout(60)
                ->withHeader('access_token', $this->subAccount->asaas_access_token_api_key)
                ->post(config('services.asaas.url') . '/pix/addressKeys', [
                    'type' => 'EVP'
                ])
                ->throw()
                ->json();

            $this->subAccount->update([
                'asaas_pix_key_id' => $response['id'],
                'asaas_pix_key' => $response['key'],
            ]);
        } catch (\Exception $e) {
            throw new ApiException('Failed to generate pix key: ' . $e->getMessage());
        }
    }
}
