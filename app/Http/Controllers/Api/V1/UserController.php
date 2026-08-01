<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\UserPermissionEnum;
use App\Http\Requests\Api\V1\User\StoreUserRequest;
use App\Http\Requests\Api\V1\User\UpdateUserRequest;
use App\Http\Resources\V1\User\UserResource;
use Illuminate\Support\Arr;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use App\Http\Services\UserService;

class UserController extends Controller
{
    public function __construct(protected UserService $userService) {}
    public function index(\Illuminate\Http\Request $request): JsonResponse
    {
        $this->authorize(UserPermissionEnum::INDEX->value);

        $users = User::latest()->paginate(5);

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

        $user = $this->userService->storeUser($request->validated());

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

        $user = $this->userService->updateUser($user, $request->validated());

        return $this->successResponse(new UserResource($user), 'Usuário atualizado com sucesso!', 200);
    }

    public function destroy(User $user): JsonResponse
    {
        $this->authorize(UserPermissionEnum::DESTROY->value);

        $user->delete();

        return $this->successResponse([], 'Usuário deletado com sucesso!', 200);
    }
}
