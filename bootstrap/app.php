<?php

use App\Http\Middleware\JwtAuthMiddleware;
use App\Support\ApiResponse;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api/index.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->appendToGroup('auth.api', [JwtAuthMiddleware::class]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->renderable(fn(AuthenticationException $e) => ApiResponse::error(message: "Your credentials are invalid!: " . $e->getMessage()));
        $exceptions->renderable(fn(AuthorizationException $e) => ApiResponse::error(message: "You don't have permission for make this action!" . $e->getMessage()));
        $exceptions->renderable(fn(ModelNotFoundException $e) => ApiResponse::error(message: "Resources not found!: " . $e->getMessage()));
        $exceptions->renderable(fn(NotFoundHttpException $e) => ApiResponse::error(message: "Route not found!: " . $e->getMessage()));
        $exceptions->renderable(fn(MethodNotAllowedException $e) => ApiResponse::error(message: "Method not found!: " . $e->getMessage()));
        $exceptions->renderable(fn(BadMethodCallException $e) => ApiResponse::error(message: "Method not found!: " . $e->getMessage()));
        $exceptions->renderable(fn(ValidationException $e) => ApiResponse::error(message: $e->getMessage()));
        $exceptions->renderable(fn(AccessDeniedHttpException $e) => ApiResponse::error(message: "Access denied!" . $e->getMessage()));
        $exceptions->renderable(fn(Throwable $e) => ApiResponse::error(message: "Server internal error!: " . $e->getMessage())); 
    })->create();

