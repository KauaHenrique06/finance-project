<?php

use App\Http\Controllers\Transaction\TransactionController;

Route::middleware('auth.api')->group(function() {
    Route::post('/', [TransactionController::class, 'store']);
    Route::get('/group/{id}', [TransactionController::class, 'indexTransactionByGroupId']);
    Route::delete('/group/{id}', [TransactionController::class, 'deleteGroup']);
    Route::patch('/{id}', [TransactionController::class, 'markTransactionAsPaid']);
    Route::post('/group/{id}/assign', [TransactionController::class, 'assignUserToTransaction']);
});