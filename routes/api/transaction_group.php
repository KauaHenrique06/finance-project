<?php

use App\Http\Controllers\Transaction\GroupTransactionController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth.api')->group(function() {
    Route::post('/', [GroupTransactionController::class, 'store'])->middleware('can:transactionGroup.create');
    Route::get('/{id}', [GroupTransactionController::class, 'show'])->middleware('can:transactionGroup.view');
    Route::delete('/{id}', [GroupTransactionController::class, 'destroy'])->middleware('can:transactionGroup.delete');
    Route::post('/{id}/participant', [GroupTransactionController::class, 'assignParticipant'])->middleware('can:transactionGroup.assignParticipant');
    Route::patch('/{id}/instance', [GroupTransactionController::class, 'assignInstanceToGroup'])->middleware('can:transactionGroup.assignInstance');
});
