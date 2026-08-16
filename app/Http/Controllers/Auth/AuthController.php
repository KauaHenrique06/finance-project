<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginUserRequest;
use App\Http\Requests\Auth\StoreUserRequest;
use App\Http\Resources\Auth\AuthResource;
use App\Services\Auth\AuthService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

/**
 * @tags Auth
 */
class AuthController extends Controller
{
    public function __construct(protected AuthService $authService) {}

    /**
     * Register a user
     *
     * Creates the account and notifies the administrators.
     *
     * @unauthenticated
     */
    public function register(StoreUserRequest $request): JsonResponse
    {
        $data = $this->authService->register($request->validated());
        return ApiResponse::success(
            new AuthResource($data),
            'User registered with success!',
            201
        );
    }

    /**
     * Log in
     *
     * Returns the JWT along with the authenticated user. Send the token as
     * `Authorization: Bearer <token>` on every other endpoint.
     *
     * @unauthenticated
     */
    public function login(LoginUserRequest $request): JsonResponse
    {
        $data = $this->authService->login($request->validated());
        return ApiResponse::success(
            $data,
            'User logged with success!',
            200
        );
    }

    /**
     * Get the authenticated user
     *
     * Returns the current profile with its roles and permissions loaded.
     */
    public function me(): JsonResponse
    {
        $data = $this->authService->me();
        return ApiResponse::success(
            new AuthResource($data),
            'Profile loaded with success',
            200
        );
    }

    /**
     * Refresh the JWT
     *
     * Exchanges the current token for a new one.
     */
    public function refreshToken(): JsonResponse
    {
        $data = $this->authService->refreshToken();
        return ApiResponse::success(
            $data,
            'Token refreshed with success!',
            200
        );
    }
}
