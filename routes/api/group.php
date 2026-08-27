<?php

use App\Http\Controllers\Group\GroupController;
use App\Http\Controllers\Message\MessageController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth.api')->group(function() {
    Route::get('/', [GroupController::class, 'index'])->middleware('can:group.view');
    Route::get('/{id}', [GroupController::class, 'indexTransactionByGroupId'])->middleware('can:group.view'); // ALERT
    Route::patch('/{id}', [GroupController::class, 'update'])->middleware('can:group.update');
    Route::delete('/{id}', [GroupController::class, 'destroy'])->middleware('can:group.delete');
    Route::post('/{id}/participant', [GroupController::class, 'assignParticipant'])->middleware('can:group.assignParticipant');
     
    // Group Messages
    Route::post('/{id}/message', [MessageController::class, 'store'])->middleware('can:group.sendMessage');
    Route::get('/{id}/message', [MessageController::class, 'index'])->middleware('can:group.viewMessage');
});
