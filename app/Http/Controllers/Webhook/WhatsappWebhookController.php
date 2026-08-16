<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Models\WhatsappInstance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WhatsappWebhookController extends Controller
{
    public function handle (Request $request)
    {
        $event = $request['event'];
        $instance = WhatsappInstance::where('slug', $request['instance'])->first();

        if (!$instance)
        {
            Log::info('Instance not found!', ['instance' => $request['instance']]);
            return response()->noContent();
        }
        
        match ($event)
        {
            'qrcode.updated' => $this->handleQrCodeUpdate($event, $instance),
            'connection.update' => $this->handleConnectionUpdate($request->toArray(), $instance),
            default => Log::debug('Event ignored: ' . $event)
        };

        return response()->noContent();
    }

    protected function handleQrCodeUpdate(string $event, WhatsappInstance $instance) 
    {
       
    }

    protected function handleConnectionUpdate(array $request, WhatsappInstance $instance) 
    {
        $state = $request['data']['state'];
        $status = match ($state) 
        {
            'close' => 'disconnected',
            'connecting' => 'connecting',
            'open' => 'connected',
            'refused' => 'refused',
            default => null
        };


        if ($status === null) {
            Log::warning('Connection state unknown!', ['instance' => $instance->name, 'state' => $state]);
            return;
        }

        return DB::transaction(function () use ($instance, $status, $request) {

            if (in_array($status, ['disconnected', 'refused'], true)) 
            {
                $instance->update(['status' => $status]);
                Log::info("Failed to connect on instance $instance->name: " . $status);
                return;
            }

            if ($status === 'connected') 
            {
                $instance->update([
                    'status' => $status,
                    'owner_jid' => $request['data']['wuid'],
                    'connected_at' => now()
                ]);
                Log::info("Instance $instance->name is connected!");
                return;
            }
        });
    }
}