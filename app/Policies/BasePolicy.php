<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BasePolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->tokenCan('*')) {
            dd('cheguei aqui');
            return true;
        }
        return null;
    }

    protected function getPermissionName(string $ability, string $modelClass): string
    {

        $modelName = Str::lower(class_basename($modelClass));
        return "{$ability} {$modelName}";
    }

    public function viewAny(User $user, string $modelClass): bool
    {
        $permission = $this->getPermissionName('index', $modelClass);
        return $user->tokenCant($permission);
    }

    public function view(User $user, Model $model): bool
    {
        $permission = $this->getPermissionName('show', $model::class);
        return $user->tokenCant($permission);
    }

    public function create(User $user, string $modelClass): bool
    {
        $permission = $this->getPermissionName('create', $modelClass);

        return $user->tokenCant($permission);
    }

    public function update(User $user, Model $model): bool
    {
        $permission = $this->getPermissionName('update', $model::class);
        return $user->tokenCant($permission);
    }


    public function delete(User $user, Model $model): bool
    {
        $permission = $this->getPermissionName('destroy', $model::class);
        return $user->tokenCan($permission);
    }
}
