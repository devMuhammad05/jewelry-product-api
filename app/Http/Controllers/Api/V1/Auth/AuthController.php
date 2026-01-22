<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Auth;

use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Auth\AuthManager;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\V1\Auth\LoginUserRequest;
use App\Http\Requests\Api\V1\Auth\RegisterUserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

final class AuthController extends ApiController
{
    public function __construct(private readonly Hasher $hasher, private readonly AuthManager $authManager)
    {
    }
    /**
     * Handle a registration request for the application.
     */
    public function register(RegisterUserRequest $request): JsonResponse
    {
        $user = User::create([
            'title' => $request->title,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'dob' => $request->dob,
            'password' => $this->hasher->make($request->password),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->successResponse('User registered successfully', [
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 201);
    }

    /**
     * Handle a login request for the application.
     */
    public function login(LoginUserRequest $request): JsonResponse
    {
        if (! $this->authManager->attempt($request->only('email', 'password'))) {
            return $this->errorResponse('Invalid login details', 401);
        }

        $user = User::where('email', $request->email)->firstOrFail();

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->successResponse('User logged in successfully', [
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * Handle a logout request for the application.
     */
    public function logout(): JsonResponse
    {
        /** @var User $user */
        $user = $this->authManager->user();

        $user->currentAccessToken()->delete();

        return $this->successResponse('User logged out successfully');
    }


    /**
     * Get the authenticated user.
     */
    public function me(): JsonResponse
    {
        return $this->successResponse('User profile retrieved successfully', $this->authManager->user());
    }
}
