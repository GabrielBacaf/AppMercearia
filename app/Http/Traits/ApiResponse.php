<?php

namespace App\Http\Traits;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use Illuminate\Validation\Validator;

trait ApiResponse
{
    /**
     * Retorna uma resposta de sucesso padronizada.
     *
     * @param mixed $data
     * @param string $message
     * @param int $status
     * @return \Illuminate\Http\JsonResponse
     */
    protected function successResponse(array|Model|JsonResource $data = []  , string $message = '', int $status): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $data
        ], $status);
    }

    /**
     * Retorna uma resposta de erro padronizada.
     *
     * @param string $message
     * @param array $errors
     * @param int $status
     * @return \Illuminate\Http\JsonResponse
     */
    protected function errorResponse(string $message, array $errors = [], int $status): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors'  => $errors
        ], $status);
    }

    /**
     * Retorna uma resposta para erros de validação.
     *
     * @param \Illuminate\Validation\Validator $validator
     * @return \Illuminate\Http\JsonResponse
     */
    protected function responseValidationError(Validator $validator): JsonResponse
    {
        return $this->errorResponse(
            'Dados inválidos.',
            $validator->errors()->toArray(),
            Response::HTTP_UNPROCESSABLE_ENTITY
        );
    }
}
