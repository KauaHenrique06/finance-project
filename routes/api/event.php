<?php 

use App\Http\Controllers\Event\EventController;
use App\Http\Controllers\Transaction\GroupTransactionController;

Route::middleware('auth.api')->group(function () {
    Route::get('/', [EventController::class, 'index']);
    Route::post('/', [EventController::class, 'store']);
    Route::get('/{id}', [EventController::class, 'show']);
    Route::delete('/{id}', [EventController::class, 'delete']);
    Route::patch('/{id}', [EventController::class, 'update']);
});