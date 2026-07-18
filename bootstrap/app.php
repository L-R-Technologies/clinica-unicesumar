<?php

use App\Http\Middleware\CheckActiveUser;
use App\Http\Middleware\CheckUserRole;
use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpFoundation\Response;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            HandleInertiaRequests::class,
        ]);

        $middleware->alias([
            'role' => CheckUserRole::class,
            'active' => CheckActiveUser::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Sessão expirada em visita Inertia — recarrega a página em vez de exibir erro 419
        $exceptions->respond(function (Response $response, Throwable $exception, Illuminate\Http\Request $request) {
            if ($response->getStatusCode() === 419) {
                return back()->with('error', 'Sua sessão expirou. Tente novamente.');
            }

            return $response;
        });
    })->create();
