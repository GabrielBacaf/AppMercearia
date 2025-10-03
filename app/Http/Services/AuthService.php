<?php

namespace App\Http\Services;


use App\Enums\UserPermissionEnum;
use App\Http\Requests\AuthRequest;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


class AuthService
{

    public function __construct(private User $user)
    {
    }

    public function login(array $credentials): string
    {
        $userAuth = $this->user->where('login', $credentials['login'])->first();

        if (!$userAuth || !Hash::check($credentials['password'], $userAuth->password)) {

            throw new AuthenticationException('Dados incorretos! Tente novamente.');
        }
        return $userAuth->createToken($credentials['device_name'])->plainTextToken;
    }
}
