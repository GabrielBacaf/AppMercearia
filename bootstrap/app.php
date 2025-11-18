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
        $exceptions->render(function (HttpException $e, $request) {

            $errorDetails = [];

            if (config('app.debug')) {
                $errorDetails['original_error'] = $e->getMessage();
            }

            return response()->json([
                'success' => false,
                'message' => 'Ocorreu um erro ao processar sua solicitação. ',
                'errors'  => $errorDetails
            ], $e->getStatusCode());
        });
    })->create();

// Registra o Kernel do Console
$app->singleton(
    ConsoleKernelContract::class,
    ConsoleKernel::class
);

return $app;
