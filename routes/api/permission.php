<?php

use App\Http\Controllers\Permission\PermissionController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth.api')->group(function () {
    Route::get('/', [PermissionController::class, 'index'])->middleware('can:permission.view');
    Route::post('/', [PermissionController::class, 'store'])->middleware('can:permission.store');
    Route::get('/{permission}', [PermissionController::class, 'show'])->middleware('can:permission.view');
    Route::patch('/{permission}', [PermissionController::class, 'update'])->middleware('can:permission.update');
    Route::delete('/{permission}', [PermissionController::class, 'destroy'])->middleware('can:permission.delete');
});
