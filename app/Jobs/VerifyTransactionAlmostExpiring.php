<?php

namespace App\Jobs;

use App\Models\Group;
use App\Models\Transaction;
use App\Models\WhatsappInstance;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class VerifyTransactionAlmostExpiring implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct() {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Comparative date
        $nextDate = now()->addDay()->toDateString();

        // Groups with expiring transactions
        Transaction::with(['group', 'group.whatsappInstance'])
            ->where('is_paid', false)
            ->whereDate('due_date', $nextDate)
            ->whereHas('group', function ($query) {
                $query->whereNotNull('instance_id');
            })->chunkById(10, function ($transactions) {
                foreach ($transactions as $transaction) 
                {
                    SendMessageAlertingGroup::dispatch($transaction);
                }
            });  
    }
}
