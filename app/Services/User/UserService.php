<?php 

namespace App\Services\User;

use App\Exceptions\ApiException;
use App\Jobs\SendForgotPasswordMail;
use App\Models\ForgotPassword;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Str;

class UserService {

    public function forgotPassword(array $data): void {

        $user = User::where('email', $data['email'])->firstOrFail();

        $forgotPassword = ForgotPassword::create([
            'user_id' => $user->id,
            'access_token' => Str::uuid7(),
            'expires_at' => now()->addHours(2)
        ]);

        SendForgotPasswordMail::dispatch($user, $forgotPassword);
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

    public function changePassword(array $data)
    {
        $authUser = Auth::user();
        $user = User::where('id', $authUser->id)->first();
        if (!$user) 
        {
            throw new Exception("You can't change password to other profile!");
        }

        if (!Hash::check($data['old_password'], $user->password)) 
        {
            throw new Exception("This old password is incorrect!");
        }

        return DB::transaction(function() use ($user, $data) {
            $user->update([
                'password' => $data['new_password']
            ]);
            return;
        });
    }

    public function update(array $data): User
    {
        $authUser = Auth::user();
        $user = User::where('id', $authUser->id)->first();
        if (!$user) 
        {
            throw new ApiException("You can't change profile infos to other profile!");
        }

        return DB::transaction(function() use ($data, $user) {

            $user->update($data);
            $user->refresh();

            if ($data['profile_pic'])
            {
                $user->addMedia($data['profile_pic'])->toMediaCollection('profile_pic');
            } 

            return $user->load(['roles', 'permissions']);
        });
    }

}