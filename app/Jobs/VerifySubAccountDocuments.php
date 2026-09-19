<?php

namespace App\Jobs;

use App\Exceptions\ApiException;
use App\Mail\SubAccountDocumentsMail;
use App\Models\AsaasSubAccount;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class VerifySubAccountDocuments implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(private AsaasSubAccount $subAccount)
    {
        $this->queue = 'sub-account-documents-mail';
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {

            $response = Http::timeout(60)
                ->withHeader('access_token', $this->subAccount->asaas_access_token_api_key)
                ->get(config('services.asaas.url') . '/myAccount/documents')
                ->throw();

        } catch (\Exception $e) {
            throw new ApiException('Failed to to establish connection with asaas API: ' . $e->getMessage());
        }

        $data = $response->json();
        $user = $this->subAccount->user;

        if (empty($data['onboardingUrl']))
        {
            Log::warning(
                "Failed to send email for user {$user->name} for activate your account!"
            );

            return;
        }

        Mail::to($user)->send(new SubAccountDocumentsMail(
            $user,
            $data['onboardingUrl'],
            $this->subAccount->commercial_info_expiration
        ));
    }
}
