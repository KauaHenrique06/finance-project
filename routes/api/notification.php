<?php 

use App\Http\Controllers\Notification\NotificationController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth.api')->group(function() {
    Route::get('/', [NotificationController::class, 'index']);
    Route::patch('/all/read', [NotificationController::class, 'markAllNotificationAsRead']);
    Route::patch('/{id}/read', [NotificationController::class, 'markNotificationAsRead']);

});