<?php

namespace App\Http\Middleware;

use App\Support\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class JwtAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        try {
            $user = JWTAuth::parseToken()->authenticate();
            if (!$user)
            {
                return ApiResponse::error('Unauthorized!', 401);
            }   
        } catch (\Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) 
            {
                return ApiResponse::error("Invalid Token!", null, 401);
            }
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException)
            {
                return ApiResponse::error("Token Expired!", null, 401);
            }

            return ApiResponse::error("Unauthorized!", null, 401);
        }   

        return $next($request);
    }
}
