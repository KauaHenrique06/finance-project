<?php 

namespace App\Services\User;

use App\Jobs\SendForgotPasswordMail;
use App\Models\ForgotPassword;
use App\Models\User;
use Exception;
use Str;

class UserService {

    public function forgotPassword(array $data): void {

        $user = User::where('email', $data['email'])->firstOrFail();

        ForgotPassword::create([
            'user_id' => $user->id,
            'access_token' => Str::uuid7(),
            'expires_at' => now()->addHours(2)
        ]);

        SendForgotPasswordMail::dispatch($user);
    }

    public function resetPassword(array $data) {

        $forgotPassword = ForgotPassword::where('access_token', $data['token'])
            ->where('used', false)
            ->where('expires_at', '>=', now())
            ->first();

        if(!$forgotPassword) {
            throw new Exception('Invalid access token!');
        }

        $user = $forgotPassword->user;
        $user->update(['password' => $data['password']]);
        $forgotPassword->update(['used' => true]);
    }

}