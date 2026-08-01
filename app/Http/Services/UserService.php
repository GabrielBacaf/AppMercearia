<?php

namespace App\Http\Services;

use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function storeUser(array $data): User
    {
        $user = User::create(Arr::except($data, ['roles']));
        $user->assignRole($data['roles']);
        
        return $user;
    }

    public function updateUser(User $user, array $data): User
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        
        $user->update(Arr::except($data, ['roles']));
        $user->assignRole($data['roles']);
        
        return $user;
    }
}
