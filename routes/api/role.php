<?php

use App\Http\Controllers\Role\RoleController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth.api')->group(function() {
    Route::get('/', [RoleController::class, 'index'])->middleware('can:role.view');
    Route::post('/', [RoleController::class, 'store'])->middleware('can:role.store');
    Route::get('/{role}', [RoleController::class, 'show'])->middleware('can:role.view');
    Route::patch('/{role}', [RoleController::class, 'update'])->middleware('can:role.update');
    Route::delete('/{role}', [RoleController::class, 'destroy'])->middleware('can:role.delete');
    Route::post('/{user}/{role}', [RoleController::class, 'storeRoleToUser'])->middleware('can:assignRole');
    Route::post('/{role}/permission', [RoleController::class, 'storePermissionToRole'])->middleware('can:role.assignPermission');
});

