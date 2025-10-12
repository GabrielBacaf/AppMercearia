<?php

namespace App\Http\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;

trait ApiResponse
{

    protected function successResponse(array|Model|JsonResource|null $data = [], string $message = '', int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $status);
    }


    protected function errorResponse(string $message, array $errors = [], int $status): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors
        ], $status);
    }

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
