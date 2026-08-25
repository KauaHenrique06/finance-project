<?php

use App\Http\Controllers\Event\EventController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth.api')->group(function () {
    Route::get('/', [EventController::class, 'index'])->middleware('can:event.view');
    Route::post('/', [EventController::class, 'store'])->middleware('can:event.create');
    Route::get('/{id}', [EventController::class, 'show'])->middleware('can:event.view');
    Route::patch('/{id}', [EventController::class, 'update'])->middleware('can:event.update');
    Route::delete('/{id}', [EventController::class, 'delete'])->middleware('can:event.delete');
    Route::patch('/{id}/instance', [EventController::class, 'assignInstanceToEvent'])->middleware('can:event.assignInstance');
});
