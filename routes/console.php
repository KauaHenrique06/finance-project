<?php

use App\Jobs\ChangeCampaignStatusAfterDue;
use App\Jobs\VerifyTransactionAlmostExpiring;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::job(VerifyTransactionAlmostExpiring::class)
    ->daily()
    ->withoutOverlapping();

Schedule::job(ChangeCampaignStatusAfterDue::class)
    ->daily()
    ->withoutOverlapping();
