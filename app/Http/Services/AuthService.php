<?php

namespace App\Http\Services;


use App\Enums\UserPermissionEnum;
use App\Http\Requests\AuthRequest;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{

    public function __construct(private User $user) {}

    public function login(array $credentials): string
    {

        $this->user = User::where('login', $credentials['login'])->first();

        if (!$this->user || !Hash::check($credentials['password'], $this->user->password)) {

            throw new AuthenticationException('Dados incorretos! Tente novamente.');
        }
        return $this->user->createToken($credentials['device_name'], [UserPermissionEnum::CREATE->value])->plainTextToken;
    }
}
