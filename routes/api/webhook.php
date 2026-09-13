<?php

use App\Http\Controllers\Webhook\AsaasWebhookController;
use App\Http\Controllers\Webhook\WhatsappWebhookController;
use Illuminate\Support\Facades\Route;

Route::post('/evolution', [WhatsappWebhookController::class, 'handle']);
Route::post('/asaas', [AsaasWebhookController::class, 'handle']);
