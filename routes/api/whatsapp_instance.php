<?php

use App\Http\Controllers\Whatsapp\WhatsappInstanceController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth.api')->group(function () {
    Route::get('/', [WhatsappInstanceController::class, 'index'])->middleware('can:whatsapp.view');
    Route::post('/', [WhatsappInstanceController::class, 'store'])->middleware('can:whatsapp.create');
    Route::delete('/{id}', [WhatsappInstanceController::class, 'delete'])->middleware('can:whatsapp.delete');
});
