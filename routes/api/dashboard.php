<?php 

use App\Http\Controllers\Dashboard\TransactionDashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth.api')->group(function () {
    Route::get('/transaction-group', [TransactionDashboardController::class, 'groupDashboard']);
});