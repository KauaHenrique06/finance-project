<?php

use App\Http\Controllers\Transaction\GroupTransactionController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth.api')->group(function() {
    Route::post('/', [GroupTransactionController::class, 'store']);
    Route::get('/{id}', [GroupTransactionController::class, 'show']);
    Route::delete('/{id}', [GroupTransactionController::class, 'destroy']);
    Route::post('/{id}/participant', [GroupTransactionController::class, 'assignParticipant']);
    Route::patch('/{id}/instance', [GroupTransactionController::class, 'assignInstanceToGroup']);
});
