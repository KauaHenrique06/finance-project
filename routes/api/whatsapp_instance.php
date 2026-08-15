<?php 

use App\Http\Controllers\Whatsapp\WhatsappInstanceController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth.api')->group(function () {
    Route::post('/', [WhatsappInstanceController::class, 'store']);
});