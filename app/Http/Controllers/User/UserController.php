<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\ChangePasswordRequest;
use App\Http\Requests\User\ForgotPasswordRequest;
use App\Http\Requests\User\ResetPasswordRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\Auth\AuthResource;
use App\Services\User\UserService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

/**
 * @tags User
 */
class UserController extends Controller
{
    public function __construct(protected UserService $userService) {}

    /**
     * Request a password reset
     *
     * Sends the recovery e-mail with the reset token.
     *
     * @unauthenticated
     */
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $this->userService->forgotPassword($request->validated());
        return ApiResponse::success(message: 'E-mail de recuperação de senha enviado com sucesso!');
    }

    /**
     * Reset the password
     *
     * Consumes the token sent by e-mail and sets the new password.
     *
     * @unauthenticated
     */
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $this->userService->resetPassword($request->validated());
        return ApiResponse::success(message: 'Password was changed with success!');
    }

    /**
     * Change the password
     *
     * Changes the password of the authenticated user.
     */
    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        $this->userService->changePassword($request->validated());
        return ApiResponse::success(message: 'Password was changed with success!');
    }

    /**
     * Update the profile
     *
     * Updates the authenticated user's own profile.
     */
    public function update(UpdateUserRequest $request): JsonResponse
    {
        $data = $this->userService->update($request->validated());
        return ApiResponse::success(
            new AuthResource($data),
            'User was updated with success!');
    }
}
