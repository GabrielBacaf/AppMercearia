<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\UserPermissionEnum;
use App\Http\Requests\Api\V1\User\StoreUserRequest;
use App\Http\Requests\Api\V1\User\UpdateUserRequest;
use App\Http\Resources\V1\User\UserResource;
use Illuminate\Support\Arr;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(): JsonResponse
    {
        $this->authorize(UserPermissionEnum::INDEX->value);

        $users = User::paginate(5);

        return $this->successResponseCollection(
            UserResource::collection($users),
            $users,
            'Usuário listados com sucesso!',
            200
        );
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        $this->authorize(UserPermissionEnum::STORE->value);

        $validatedData = $request->validated();

        $user = User::create(Arr::except($validatedData, ['roles']));
        $user->assignRole($validatedData['roles']);

        return $this->successResponse(new UserResource($user), 'Usuário criado com sucesso!', 201);
    }

    public function show(User $user): JsonResponse
    {
        $this->authorize(UserPermissionEnum::SHOW->value);

        return $this->successResponse(new UserResource($user), 'Usuário detalhado com sucesso!', 200);
    }

    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $this->authorize(UserPermissionEnum::UPDATE->value);

        $validatedData = $request->validated();

        if (isset($validatedData['password'])) {
            $validatedData['password'] = Hash::make($validatedData['password']);
        }
        $user->update(Arr::except($validatedData, ['roles']));
        $user->assignRole($validatedData['roles']);

        return $this->successResponse(new UserResource($user), 'Usuário atualizado com sucesso!', 200);
    }

    public function destroy(User $user): JsonResponse
    {
        $this->authorize(UserPermissionEnum::DESTROY->value);

        $user->delete();

        return $this->successResponse([], 'Usuário deletado com sucesso!', 200);
    }
}
