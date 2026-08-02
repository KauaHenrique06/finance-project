<?php

namespace App\Services\Auth;

use App\Http\Resources\Auth\AuthResource;
use App\Jobs\SendForgotPasswordMail;
use App\Jobs\SendWelcomeEmail;
use App\Models\ForgotPassword;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Str;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthService {

    public function register(array $data): User {

        $usersToNotificate = User::whereHas('roles', function($query) {
            $query->whereIn('name', ['admin']);
        })->pluck('id')->toArray();

        return DB::transaction(function() use ($data, $usersToNotificate) {

            $user = User::create($data);
            $user->refresh();

            foreach($usersToNotificate as $id) {
                Notification::create([
                    'user_id' => $id,
                    'type' => 'new_user',
                    'message' => "User {$user->name} has just registered in the system!",
                    'data' => [
                        'new_user_id' => $user->id,
                        'user_notified_id' => $id
                    ]
                ]);
            }

            SendWelcomeEmail::dispatch($user);

            return $user;
        });
    }

    public function login(array $data): array {

        $user = User::where('email', $data['email'])->first();

        if($user && Auth::attempt(['email' => $data['email'], 'password' => $data['password']])) {

            $refreshTokenInSec = Config::get('jwt.refresh_ttl') * 60;
            $token = JWTAuth::fromUser($user);

            $user->load(['roles', 'permissions']);
            return [
                'user' => new AuthResource($user),
                'token' => $token,
                'refresh_in' => $refreshTokenInSec
            ];
        }

        throw new AuthenticationException();
    }

    public function me(): User {

        $authUser = Auth::user();
        $authUser->load('addresses', 'roles', 'permissions');
        return $authUser;

    }

    public function refreshToken() {

        $refreshTokenInSec = Config::get('jwt.refresh_ttl') * 60;
        $token = auth('api')->refresh();

        return [
            'token' => $token,
            'refresh_in' => $refreshTokenInSec
        ];

    }
}
