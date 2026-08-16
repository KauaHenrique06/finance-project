<?php

namespace App\Observers;

use App\Events\WhatsappInstanceEvent;
use App\Models\WhatsappInstance;

class WhatsappInstanceObserver
{
    /**
     * Handle the WhatsappInstance "created" event.
     */
    public function created(WhatsappInstance $whatsappInstance): void
    {
        //
    }

    /**
     * Handle the WhatsappInstance "updated" event.
     */
    public function updated(WhatsappInstance $whatsappInstance): void
    {
        if ($whatsappInstance->wasChanged(['status', 'qrcode_base64']))
        {
            WhatsappInstanceEvent::dispatch($whatsappInstance);
        }
    }

    /**
     * Handle the WhatsappInstance "deleted" event.
     */
    public function deleted(WhatsappInstance $whatsappInstance): void
    {
        //
    }

    /**
     * Handle the WhatsappInstance "restored" event.
     */
    public function restored(WhatsappInstance $whatsappInstance): void
    {
        //
    }

    /**
     * Handle the WhatsappInstance "force deleted" event.
     */
    public function forceDeleted(WhatsappInstance $whatsappInstance): void
    {
        //
    }
}
