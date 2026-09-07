<?php

namespace App\Jobs;

use App\Enum\CampaignStatusEnum;
use App\Models\Campaign;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ChangeCampaignStatusAfterDue implements ShouldQueue
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
        $today = now()->toDateString();
        
        Campaign::whereNotNull('due_date')
            ->whereDate('due_date', $today) 
            ->update(['status' => CampaignStatusEnum::FINISHED->value]);
    }
}
