<?php

use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(base_path('routes/api/auth.php'));
Route::prefix('role')->group(base_path('routes/api/role.php'));
Route::prefix('permission')->group(base_path('routes/api/permission.php'));
Route::prefix('user')->group(base_path('routes/api/user.php'));
