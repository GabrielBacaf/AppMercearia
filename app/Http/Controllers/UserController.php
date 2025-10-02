<?php

namespace App\Http\Controllers;


use App\Http\Services\UserService;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    public function __construct(protected UserService $userService)
    {
        $this->authorizeResource(User::class, 'user');
    }

    public function index(): JsonResponse
    {
        $users = User::paginate(15);
        return response()->json($users);
    }


    public function store(StoreUserRequest $request): JsonResponse
    {
        $validatedData = $request->validated();

        $user = User::create($validatedData);

        return response()->json([
            'data'    => $user,
            'message' => 'Usuário criado com sucesso!',
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user): JsonResponse
    {
        return response()->json($user);
    }

    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $validatedData = $request->validated();

        if (isset($validatedData['password'])) {
            $validatedData['password'] = Hash::make($validatedData['password']);
        }

        $user->update($validatedData);

        return response()->json($user);
    }


    public function destroy(User $user): JsonResponse
    {
        $user->delete();
        return response()->json(null, 204);
    }
}
