<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginUserRequest;
use App\Http\Requests\Auth\StoreUserRequest;
use App\Http\Resources\Auth\AuthResource;
use App\Services\Auth\AuthService;
use App\Support\ApiResponse;

class AuthController extends Controller
{

    public function __construct(protected AuthService $authService) {}

    public function register(StoreUserRequest $request) {

        $data = $this->authService->register($request->validated());
        return ApiResponse::success(
            new AuthResource($data),
            'User registered with success!',
            201
        );
    }

    public function login(LoginUserRequest $request) {

        $data = $this->authService->login($request->validated());
        return ApiResponse::success(
            $data,
            'User logged with success!',
            200
        );
    }

    public function me() {

        $data = $this->authService->me();
        return ApiResponse::success(
            new AuthResource($data),
            'Profile loaded with success',
            200
        );

    }

    public function refreshToken() {

        $data = $this->authService->refreshToken();
        return ApiResponse::success(
            $data,
            'Token refreshed with success!',
            200
        );

    }
}
