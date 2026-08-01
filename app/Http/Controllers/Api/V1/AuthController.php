<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Controller;;
use App\Http\Requests\Api\V1\Auth\AuthRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;


use App\Http\Services\AuthService;

class AuthController extends Controller
{
    public function __construct(protected AuthService $authService) {}

    public function login(AuthRequest $request): JsonResponse
    {
        $tokenData = $this->authService->login($request->validated(), $request->device_name);

        if (!empty($tokenData)) {
            return $this->successResponse(
                $tokenData,
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
