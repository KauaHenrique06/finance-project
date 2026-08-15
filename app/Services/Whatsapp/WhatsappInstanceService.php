<?php

namespace App\Services\Whatsapp;

use App\Exceptions\ApiException;
use App\Models\WhatsappInstance;
use App\Support\Ownership;
use Auth;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Str;

class WhatsappInstanceService
{
    public function store(array $data): WhatsappInstance
    {
        $slug = Str::slug($data['name']);
        $payload = [
            'instanceName' => $slug, 
            'qrcode' => true,
            'number' => $data['number'],
            'integration' => 'WHATSAPP-BAILEYS',
            'webhook' => [
                'enabled' => true,
                'url' => env('WEBHOOK_GLOBAL_URL'),
                'events' => [
                    'CONNECTION_UPDATE',
                    // 'MESSAGES_UPSERT',
                    // 'SEND_MESSAGE'
                ]
            ]
        ];

        try { 

            $response = Http::retry(3, 30)->withHeaders(['apikey' => env('AUTHENTICATION_API_KEY')])
                ->post(env('EVOLUTION_API_URL') . '/instance/create', $payload);
                
            $response->json();

        } catch (RequestException $e) {

            throw new ApiException('Failed to create instance: ' . $e->getMessage());
        }

        return DB::transaction(function () use ($response, $data, $slug) {

            $qrCodeData = $response['qrcode'];
            $instanceData = $response['instance'];

            $whatsapp = WhatsappInstance::create([
                'name' => $data['name'],
                'number' => $data['number'],
                'evolution_id' => $instanceData['instanceId'], 
                'status' => $instanceData['status'],
                'qrcode_base64' => $qrCodeData['base64'],
                'qrcode_code' => $qrCodeData['code'],
                'user_id' => Auth::id(),
                'slug' => $slug
            ]);

            return $whatsapp;
        });
    }
}