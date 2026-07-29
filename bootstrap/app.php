<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Contracts\Console\Kernel as ConsoleKernelContract;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Console\Kernel as ConsoleKernel;

// Cria a aplicação
$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (\Throwable $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                $errorDetails = [];

                if (config('app.debug')) {
                    $errorDetails['original_error'] = $e->getMessage();
                    $errorDetails['trace'] = $e->getTrace();
                }

                $statusCode = 500;
                if ($e instanceof HttpException) {
                    $statusCode = $e->getStatusCode();
                } elseif ($e instanceof \Illuminate\Auth\AuthenticationException) {
                    $statusCode = 401;
                } elseif ($e instanceof \Illuminate\Auth\Access\AuthorizationException) {
                    $statusCode = 403;
                } elseif ($e instanceof \Illuminate\Validation\ValidationException) {
                    $statusCode = $e->status;
                    $errorDetails['validation'] = $e->errors();
                } elseif ($e instanceof \DomainException || $e instanceof \InvalidArgumentException) {
                    $statusCode = 400;
                }

                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage() ?: 'Ocorreu um erro ao processar sua solicitação.',
                    'errors'  => $errorDetails
                ], $statusCode);
            }

        });
    })->create();

// Registra o Kernel do Console
$app->singleton(
    ConsoleKernelContract::class,
    ConsoleKernel::class
);

return $app;
