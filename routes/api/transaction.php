<?php

use App\Http\Controllers\Transaction\TransactionController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth.api')->group(function() {
    Route::patch('/{id}/paid', [TransactionController::class, 'markTransactionAsPaid'])->middleware('can:transaction.pay');
});
