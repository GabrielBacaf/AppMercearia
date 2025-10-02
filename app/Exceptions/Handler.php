<?php

namespace App\Exceptions;

// 1. IMPORTANTE: Adicione estes 'use' statements no topo do arquivo
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Log;
use Throwable;

class Handler extends ExceptionHandler
{
    // ... outras propriedades como $dontReport ...

    /**
     * Register the exception handling callbacks for the application.
     */

    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        // Modifique o bloco para este
        $this->renderable(function (NotFoundHttpException $e, $request) {
            if ($request->wantsJson()) {

                // Adicione esta linha para depuração
                Log::info('NotFoundHttpException Handler foi acionado pela URL: ' . $request->url());

                return response()->json(['message' => 'Recurso não encontrado.'], 404);
            }
        });

        // Adicione também este bloco para ser mais específico
        $this->renderable(function (ModelNotFoundException $e, $request) {
            if ($request->wantsJson()) {

                // Adicione esta linha para depuração
                Log::info('ModelNotFoundException Handler foi acionado para o model: ' . $e->getModel());

                return response()->json(['message' => 'O registro específico não foi encontrado.'], 404);
            }
        });
    }
}
