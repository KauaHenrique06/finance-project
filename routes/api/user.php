<?php

use App\Http\Controllers\Asaas\SubAccountController;
use App\Http\Controllers\User\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/forgot-password', [UserController::class, 'forgotPassword']);
Route::post('/reset-password', [UserController::class, 'resetPassword']);

Route::middleware('auth.api')->group(function() {
    Route::patch('/{id}', [UserController::class, 'update'])->middleware('can:user.update');
    Route::patch('/change-password/{id}', [UserController::class, 'changePassword'])->middleware('can:user.update');

    Route::prefix('/sub-account')->group(function () {
        Route::post('/', [SubAccountController::class, 'store']);
        Route::delete('/', [SubAccountController::class, 'destroy']);
    });
});
