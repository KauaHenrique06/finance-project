<?php 

use App\Http\Controllers\User\UserController;

// Rotas não autenticadas
Route::post('/forgot-password', [UserController::class, 'forgotPassword']);
Route::post('/reset-password', [UserController::class, 'resetPassword']);

Route::middleware('auth.api')->group(function() {
    Route::post('/change-password', [UserController::class, 'resetPassword']);
});