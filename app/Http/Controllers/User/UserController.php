<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\ChangePasswordRequest;
use App\Http\Requests\User\ForgotPasswordRequest;
use App\Http\Requests\User\ResetPasswordRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Services\User\UserService;
use App\Support\ApiResponse;

class UserController extends Controller
{

    public function __construct(protected UserService $userService) {}

    public function forgotPassword(ForgotPasswordRequest $request) {
        $this->userService->forgotPassword($request->validated());
        return ApiResponse::success(message: 'E-mail de recuperação de senha enviado com sucesso!');
    }

    public function resetPassword(ResetPasswordRequest $request) {
        $this->userService->resetPassword($request->validated());
        return ApiResponse::success(message: 'Password was changed with success!');
    }

    public function changePassword(ChangePasswordRequest $request) 
    {
        $this->userService->changePassword($request->validated());
        return ApiResponse::success(message: 'Password was changed with success!');
    }

    public function update(UpdateUserRequest $request) 
    {
        $data = $this->userService->update($request->validated());
        return ApiResponse::success($data, 'User was updated with success!');
    }
}
