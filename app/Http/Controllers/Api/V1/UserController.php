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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use function Laravel\Prompts\error;

class UserController extends Controller
{

    public function __construct(protected UserService $userService) {}

    public function index(): JsonResponse
    {
        // if (!Auth::user()->tokenCant(UserPermissionEnum::INDEX->value)) {
        //     return $this->errorResponse(message: 'Usuário não Autorizado!', errors: [], status: 403);
        // }

        $this->authorize('viewAny', User::class);

        $users = User::paginate(5);

        return $this->successResponse(
            new UserResource($users),
            'Lista de usuários',
            200
        );
    }


    public function store(StoreUserRequest $request): JsonResponse
    {
        // if (!Auth::user()->tokenCant(UserPermissionEnum::CREATE->value)) {
        //     return $this->errorResponse(message: 'Usuário não Autorizado!', errors: [], status: 403);
        // }

        // dd(Auth::check(), Auth::user());

        $this->authorize('create', User::class);

        $validatedData = $request->validated();

        $user = User::create($validatedData);

        return $this->successResponse(new UserResource($user), 'Usuário criado com sucesso!', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user): JsonResponse
    {
        if (!Auth::user()->tokenCant(UserPermissionEnum::SHOW->value)) {
            return $this->errorResponse(message: 'Usuário não Autorizado!', errors: [], status: 403);
        }
        return $this->successResponse(UserResource::array($user), 'Detalhes do usuario', 200);
    }

    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        if (!Auth::user()->tokenCant(UserPermissionEnum::UPDATE->value)) {
            return $this->errorResponse(message: 'Usuário não Autorizado!', errors: [], status: 403);
        }
        $validatedData = $request->validated();

        if (isset($validatedData['password'])) {
            $validatedData['password'] = Hash::make($validatedData['password']);
        }
        $user->update($validatedData);

        return $this->successResponse(new UserResource($user), 'User atualizado com sucesso!', 200);
    }


    public function destroy(User $user): JsonResponse
    {
        if (Auth::user()->tokenCant(UserPermissionEnum::DESTROY->value)) {
            return $this->errorResponse(message: 'Usuário não Autorizado!', errors: [], status: 403);
        }
        $user->delete();
        return $this->successResponse(message: 'Usuário Deletado com sucesso!', status: 204);
    }
}
