<?php

use App\Http\Controllers\Campaign\CampaignController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth.api')->group(function () {
    Route::get('/', [CampaignController::class, 'index']);
    Route::post('/', [CampaignController::class, 'store']);
    Route::get('/{id}', [CampaignController::class, 'show']);
    Route::patch('/{id}', [CampaignController::class, 'update']);
    Route::delete('/{id}', [CampaignController::class, 'destroy']);
});