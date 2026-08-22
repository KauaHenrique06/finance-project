<?php

use App\Http\Controllers\Message\MessageController;
use App\Http\Controllers\Transaction\GroupTransactionController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth.api')->group(function() {
    Route::get('/', [GroupTransactionController::class, 'index'])->middleware('can:transactionGroup.view');
    Route::post('/', [GroupTransactionController::class, 'store'])->middleware('can:transactionGroup.create');
    Route::get('/{id}', [GroupTransactionController::class, 'indexTransactionByGroupId'])->middleware('can:transactionGroup.view');
    Route::patch('/{id}', [GroupTransactionController::class, 'update'])->middleware('can:transactionGroup.update');
    Route::delete('/{id}', [GroupTransactionController::class, 'destroy'])->middleware('can:transactionGroup.delete');
    Route::post('/{id}/participant', [GroupTransactionController::class, 'assignParticipant'])->middleware('can:transactionGroup.assignParticipant');
    Route::patch('/{id}/instance', [GroupTransactionController::class, 'assignInstanceToGroup'])->middleware('can:transactionGroup.assignInstance');
    
    // Group Messages
    Route::post('/{id}/message', [MessageController::class, 'store'])->middleware('can:transactionGroup.sendMessage');
    Route::get('/{id}/message', [MessageController::class, 'index'])->middleware('can:transactionGroup.viewMessage');
});
