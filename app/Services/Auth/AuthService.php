<?php

namespace App\Services\Auth;

use App\Http\Resources\Auth\AuthResource;
use App\Jobs\SendWelcomeEmail;
use App\Models\Address;
use App\Models\Notification;
use App\Models\User;
use App\Services\Address\AddressService;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthService {

    public function __construct(protected AddressService $addressService) {}

    public function register(array $data): User {

        $usersToNotificate = User::whereHas('roles', function($query) {
            $query->whereIn('name', ['admin']);
        })->pluck('id')->toArray();

        $address = !empty($data['address']) 
            ? $data['address']
            : null; 

        return DB::transaction(function() use ($data, $usersToNotificate, $address) {

            if ($address && is_array($address)) {
                $this->addressService->store($address);
            }

            // $data = array_merge($data, ['address_id' => $createAddress->id]);
            $user = User::create($data);
            $user->refresh();

            if (isset($data['profile_pic'])) 
            {
                if ($data['profile_pic'] instanceof UploadedFile)
                {
                    $user->addMedia($data['profile_pic'])->toMediaCollection('profile_pic');
                } else {
                    throw new Exception('The image must be an instance of UploadedFile');
                }
            }

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
        $authUser->load('roles', 'permissions');
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
