<?php

namespace App\Services\Asaas;

use App\Exceptions\ApiException;
use App\Models\AsaasSubAccount;
use App\Models\Campaign;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;

class PaymentService
{
    public function generatePaymentQrCode(
        array $data, 
        AsaasSubAccount $subAccount, 
        string $campaignId
    ): array {
        $payload = [
            'addressKey' => $subAccount->asaas_pix_key,
            'format' => 'PAYLOAD',
            'allowsMultiplePayments' => true,
            'externalReference' => $campaignId,
            'description' => $data['description'] ?? null,
        ];

        if (!empty($data['due_date'])) {
            $payload['expirationDate'] = Carbon::parse($data['due_date'])
                ->endOfDay()
                ->format('Y-m-d H:i:s');
        }

        try {
            $response = Http::timeout(60)
                ->withHeader('access_token', $subAccount->asaas_access_token_api_key)
                ->post(config('services.asaas.url') . '/pix/qrCodes/static', $payload)
                ->throw();
        } catch (Exception $e) {
            throw new ApiException("Failed to generate a qr code: " . $e->getMessage());
        }

        return $response->json();
    }

    public function deletePaymentQrCode(Campaign $campaign): void
    {
        $subAccount = AsaasSubAccount::where('user_id', $campaign->owner_id)
            ->firstOrFail();

        try {
            Http::timeout(60)
                ->withHeader('access_token', $subAccount->asaas_access_token_api_key)
                ->withUrlParameters(['id' => $campaign->asaas_qr_code_id])
                ->delete(config('services.asaas.url') . '/pix/qrCodes/static/{id}')
                ->throw();
        } catch (Exception $e) {
            throw new ApiException("Failed to delete qr code: " . $e->getMessage());
        }
    }
}