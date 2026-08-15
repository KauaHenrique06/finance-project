<?php

namespace App\Services\Whatsapp;

use App\Exceptions\ApiException;
use App\Models\GroupTransaction;
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

    public function index(array $data): LengthAwarePaginator
    {
        return WhatsappInstance::paginate($data['perPage'], ['*'], 'page', $data['page']);
    }

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
                'url' => config('services.evolution.webhook_url'),
                'events' => [
                    'CONNECTION_UPDATE',
                    // 'MESSAGES_UPSERT',
                    // 'SEND_MESSAGE'
                ]
            ]
        ];

        try { 

            $response = Http::retry(3, 30)->withHeaders(['apikey' => config('services.evolution.key')])
                ->post(config('services.evolution.url') . '/instance/create', $payload);
                
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

    public function delete(array $data): void
    {
        $instance = WhatsappInstance::findOrFail($data['id']);
        Ownership::verify($instance->user_id, "You can't delete this instance!");

        try {

            Http::retry(3, 30)->withHeaders(['apikey' => config('services.evolution.key')])
                ->delete(config('services.evolution.url') . '/instance/delete/' . $instance->slug);
        } catch (\Exception $e) {
            throw new ApiException('Failed to delete instance on evolutionAPI: ' . $e->getMessage());
        }

        $instance->delete();
    }
}