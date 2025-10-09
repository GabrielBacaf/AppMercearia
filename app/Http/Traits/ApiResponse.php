<?php

namespace App\Http\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Symfony\Component\HttpFoundation\Response;
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
    protected function successResponse(array|Model|JsonResource|null $data = [], string $message = '', int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data
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
            'errors' => $errors
        ], $status);
    }

    /**
     * Retorna uma resposta de sucesso padronizada.
     *
     * @param mixed $data
     * @param string $message
     * @param int $status
     * @param mixed $resource
     * @return \Illuminate\Http\JsonResponse
     */
    protected function successResponseCollection(array|Model|JsonResource|null $data = [], mixed $resource, string $message = '', int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'meta' => [
                'total' => $resource->total(),
                'per_page' => $resource->perPage(),
                'current_page' => $resource->currentPage(),
                'last_page' => $resource->lastPage(),
            ],
        ], $status);
    }
}
