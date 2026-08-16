<?php

use App\Http\Controllers\Notification\NotificationController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth.api')->group(function() {
    Route::get('/', [NotificationController::class, 'index'])->middleware('can:notification.view');
    Route::patch('/all/read', [NotificationController::class, 'markAllNotificationAsRead'])->middleware('can:notification.update');
    Route::patch('/{id}/read', [NotificationController::class, 'markNotificationAsRead'])->middleware('can:notification.update');
});
