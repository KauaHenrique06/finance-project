<?php 

use App\Http\Controllers\Whatsapp\WhatsappInstanceController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth.api')->group(function () {
    Route::get('/', [WhatsappInstanceController::class, 'index']);
    Route::post('/', [WhatsappInstanceController::class, 'store']);
    Route::delete('/{id}', [WhatsappInstanceController::class, 'delete']);
});