<?php

namespace app\Http\Controllers\Auth;

use app\Http\Requests\UserLoginRequest;
use app\Http\Requests\UserStoreRequest;
use app\Http\Resources\UserResource;
use app\Models\User;
use Exception;
use helpers\HttpHelpers;

class AuthController
{
    /**
     * Show registration form
     */
    public function showRegisterForm(): void
    {
        require __DIR__ . '/../../../../views/register.php';
    }

    /**
     * Show login form
     */
    public function showLoginForm(): void
    {
        require __DIR__ . '/../../../../views/login.php';
    }

    /**
     * @throws Exception
     */
    public function register(UserStoreRequest $request): void
    {
        $data = $request->validated();

        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);

        $data = User::create($data);

        if (!$data) {
            HttpHelpers::responseJson(['message' => 'Failed to create user'], 500);
        }

        $userResource = UserResource::make($data);

        HttpHelpers::responseJson([
            'message' => 'User created successfully',
            'data' => $userResource,
        ], 201);
    }

    public function login(UserLoginRequest $request): void
    {
        $data = $request->validated();

        $user = User::where('email', $data['email']);
        if (!password_verify($data['password'], $user->password)) {
            HttpHelpers::responseJson(['error' => 'Invalid credentials'], 401);
        }

        HttpHelpers::responseJson([
            'message' => 'User logged in successfully',
            'data' => UserResource::make($user),
            'token' => User::createToken($user->id)
        ], 201);
    }
}