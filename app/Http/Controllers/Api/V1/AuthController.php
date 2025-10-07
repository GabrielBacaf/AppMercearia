<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\UserPermissionEnum;
use App\Http\Controllers\Api\V1\Controller;;

use App\Http\Requests\Api\V1\Auth\AuthRequest;
use App\Http\Resources\V1\User\UserResource;
use App\Http\Services\AuthService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function __construct(protected AuthService $authService) {}

    public function login(AuthRequest $request): JsonResponse
    {
        if (Auth::attempt($request->only('login', 'password'))) {
            return $this->successResponse(
                [
                    'access_token' => $request->user()?->createToken($request->device_name)->plainTextToken,
                    'token_type' => 'Bearer',
                ],
                'Seja bem-vindo(a)!',
                200,
            );
        }
        return $this->errorResponse('Not Authorized', [], 401);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        return $this->successResponse([], 'Token Revoked', 200);
    }
}
