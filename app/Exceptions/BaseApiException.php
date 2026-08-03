<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

abstract class BaseApiException extends Exception
{
    protected int $statusCode = 400;

    protected string $title = 'Ocorreu um erro';

    protected array $details = [];

    public function render(Request $request): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $this->title,
            'error'   => $this->getMessage(),
        ];

        if (!empty($this->details)) {
            $response['details'] = $this->details;
        }

        return response()->json($response, $this->statusCode);
    }
}
