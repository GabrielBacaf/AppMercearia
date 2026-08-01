<?php

namespace App\Http\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function login(array $credentials, string $deviceName): array
    {
        $user = User::where('login', $credentials['login'])->first();

        if ($user && Hash::check($credentials['password'], $user->password)) {
            return [
                'access_token' => $user->createToken($deviceName)->plainTextToken,
                'token_type' => 'Bearer',
            ];
        }

        return [];
    }
}
