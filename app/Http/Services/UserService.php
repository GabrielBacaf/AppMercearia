<?php
namespace App\Http\Services;

use App\Models\User;

class UserService
{

    public function __construct(private User $user)
    {
    }

}
