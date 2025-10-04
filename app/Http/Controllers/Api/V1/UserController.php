<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\UserPermissionEnum;
use App\Http\Requests\Api\V1\User\StoreUserRequest;
use App\Http\Requests\Api\V1\User\UpdateUserRequest;
use App\Http\Resources\V1\User\UserResource;
use App\Http\Services\UserService;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;


class UserController extends Controller
{

    public function __construct(protected UserService $userService) {}

    public function index(): JsonResponse
    {
        $this->authorize(UserPermissionEnum::INDEX->value);

        $users = User::paginate(5);
        return $this->successResponse(UserResource::collection($users), 'Lista de usuários ');
    }


    public function store(StoreUserRequest $request): JsonResponse
    {
        $this->authorize(UserPermissionEnum::CREATE->value);

        $validatedData = $request->validated();

        $user = User::create($validatedData);

        return $this->successResponse(new UserResource($user), 'Usuário criado com sucesso!', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user): JsonResponse
    {
        $this->authorize(UserPermissionEnum::SHOW->value);
        return response()->json($user);
    }

    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {


        $validatedData = $request->validated();

        if (isset($validatedData['password'])) {
            $validatedData['password'] = Hash::make($validatedData['password']);
        }
        $user->update($validatedData);

        return response()->json([
            'data' => $user,
            'message' => 'Usuário atualizado com sucesso!',
        ]);
    }


    public function destroy(User $user): JsonResponse
    {
        $user->delete();

        return response()->json([
            'message' => 'Usuário deletado com sucesso!',
        ], 204);
    }
}
