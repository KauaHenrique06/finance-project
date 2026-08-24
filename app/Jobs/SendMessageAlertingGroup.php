<?php

namespace App\Jobs;

use App\Models\Group;
use App\Models\Notification;
use App\Models\Transaction;
use App\Models\User;
use App\Models\WhatsappInstance;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendMessageAlertingGroup implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private Transaction $transaction,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        $group = Group::with(['participant', 'owner'])->findOrFail($this->transaction->group_id);
        $instance = WhatsappInstance::findOrFail($group->instance_id);

        if ($instance->status !== 'connected')
        {
            Notification::create([
                'message' => "The instance {$instance->name} of this group is disconnected!",
                'type' => 'instance_disconnect',
                'user_id' => $group->owner_id,
                'data' => [
                    'instance_id' => $instance->id,
                    'instance_name' => $instance->name,
                    'owner_id' => $group->owner_id, 
                    'owner_name' => $group->owner->name, 
                ],
            ]);
        }

        $quantityInstallment = Transaction::where('group_id', $group->id)->count();

        $template = $quantityInstallment > 1
            ? config('message.almostExpiring.withInstallment')
            : config('message.almostExpiring.single');

        // Owner is not stored on the pivot table, so it has to be merged in
        $recipients = $group->participant
            ->merge([$group->owner])
            ->filter()
            ->unique('id');

        foreach ($recipients as $participant)
        {
            $message = $this->formatMessage($template, $group, $participant, $quantityInstallment);

            $this->sendMessage($instance, $participant, $message);
        }

        return;
    }

    protected function formatMessage(string $template, Group $group, User $participant, int $quantityInstallment): string
    {
        return strtr($template, [
            '{name}' => $participant->name,
            '{group}' => $group->title,
            '{title}' => $this->transaction->title,
            '{installment}' => $this->transaction->installment_number,
            '{total}' => $quantityInstallment,
            '{amount}' => number_format($this->transaction->amount, 2, ',', '.'),
            '{dueDate}' => $this->transaction->due_date->format('d/m/Y'),
        ]);
    }

    protected function sendMessage (WhatsappInstance $instance, User $participant, string $message)
    {
        $payload = [
            'number' => $participant->phone,
            'text' => $message 
        ];

        try {

            Http::withHeaders(['apikey' => config('services.evolution.key')])
                ->post(config('services.evolution.url') . '/message/sendText/' . $instance->slug, $payload)
                ->throw();

        } catch (\Exception $e) {
            Log::warning('Failed to send message: ' . $e->getMessage());
        }
    }
}
