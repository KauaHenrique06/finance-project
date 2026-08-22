<?php

use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(base_path('routes/api/auth.php'));
Route::prefix('role')->group(base_path('routes/api/role.php'));
Route::prefix('permission')->group(base_path('routes/api/permission.php'));
Route::prefix('user')->group(base_path('routes/api/user.php'));
Route::prefix('transaction')->group(base_path('routes/api/transaction.php'));
Route::prefix('transaction-group')->group(base_path('routes/api/transaction_group.php'));
Route::prefix('notification')->group(base_path('routes/api/notification.php'));
Route::prefix('whatsapp-instance')->group(base_path('routes/api/whatsapp_instance.php'));
Route::prefix('webhook')->group(base_path('routes/api/webhook.php'));
Route::prefix('dashboard')->group(base_path('routes/api/dashboard.php'));
