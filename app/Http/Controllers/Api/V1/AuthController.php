<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Controller;;
use App\Http\Requests\Api\V1\Auth\AuthRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;


class AuthController extends Controller
{
    public function __construct() {}

    public function login(AuthRequest $request): JsonResponse
    {
        $user = User::where('login', $request->login)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            return $this->successResponse(
                [
                    'access_token' => $user->createToken($request->device_name)->plainTextToken,
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
