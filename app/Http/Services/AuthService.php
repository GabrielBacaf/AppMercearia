<?php
namespace App\Http\Services;

use App\Enums\UserPermissionEnum;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{

    public function __construct(private User $user)
    {
    }

    public function login($request)
    {

        $this->user::where('login', $request->login)->first();

        if (!$this->user || !Hash::check($request->password, $this->user->password)) {
            throw ValidationException::withMessages([
                'message' => ['Dados incorretos! Tenta novamente.'],
            ]);
        }
        return $this->user->createToken($request->device_name,[UserPermissionEnum::CREATE->value])->plainTextToken;
    }

}


