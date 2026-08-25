<?php 

use App\Http\Controllers\Dashboard\TransactionDashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth.api')->group(function () {
    Route::get('/group', [TransactionDashboardController::class, 'groupDashboard'])->middleware('can:dashboard.view');
});