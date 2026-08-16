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
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api/index.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withBroadcasting(
        __DIR__.'/../routes/channels.php',
        ['middleware' => ['auth.api']],
    )

    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->appendToGroup('auth.api', [JwtAuthMiddleware::class]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->renderable(fn(AuthenticationException $e) => ApiResponse::error(message: "Your credentials are invalid!: " . $e->getMessage(), code: 401));
        $exceptions->renderable(fn(AuthorizationException $e) => ApiResponse::error(message: "You don't have permission for make this action!" . $e->getMessage(), code: 403));
        $exceptions->renderable(fn(NotFoundHttpException $e) => ApiResponse::error(
            message: $e->getPrevious() instanceof ModelNotFoundException
                ? "Resources not found!: " . $e->getPrevious()->getMessage()
                : "Route not found!: " . $e->getMessage(),
            code: 404
        ));
        $exceptions->renderable(fn(MethodNotAllowedHttpException $e) => ApiResponse::error(message: "Method not allowed!: " . $e->getMessage(), code: 405));
        $exceptions->renderable(fn(BadMethodCallException $e) => ApiResponse::error(message: "Method not found!: " . $e->getMessage(), code: 500));
        $exceptions->renderable(fn(ValidationException $e) => ApiResponse::error(message: $e->getMessage(), code: 422));
        $exceptions->renderable(fn(AccessDeniedHttpException $e) => ApiResponse::error(message: "Access denied!" . $e->getMessage(), code: 403));
        $exceptions->renderable(fn(Throwable $e) => ApiResponse::error(message: "Server internal error!: " . $e->getMessage(), code: 500));
    })->create();

