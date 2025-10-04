<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Controller;;
use App\Http\Requests\Api\V1\Auth\AuthRequest;
use App\Http\Services\AuthService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function __construct(protected AuthService $authService) {}


    public function login(AuthRequest $request): JsonResponse
    {
        try {
            $token = $this->authService->login($request->validated());
        } catch (AuthenticationException $e) {
            return response()->json([
                'message' => 'Credenciais inválidas.'
            ], Response::HTTP_UNAUTHORIZED);
        }

        return response()->json([
            'message' => 'Login bem-sucedido!',
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], Response::HTTP_OK);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()?->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout realizado com sucesso!'
        ], Response::HTTP_OK);
    }
}
