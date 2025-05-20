<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthService
{
    /**
     * Register a new user.
     *
     * @param array $data
     * @return User
     */
    public function registerUser(array $attributes): User
    {
        return User::create($attributes);
    }

    /**
     * Attempt to log the user in and generate a token.
     *
     * @param array $credentials
     * @return array ['user' => User, 'token' => string]
     * @throws ValidationException
     */
    public function loginUser(array $credentials): array
    {
        if (!Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => [trans('auth.failed')],
            ]);
        }

        $user = Auth::user();
        $token = $this->createTokenUser($user);

        return ['user' => $user, 'token' => $token];
    }

    /**
     * Log the user out (revoke the current token).
     *
     * @param User $user
     * @return void
     */
    public function logoutUser(User $user): void
    {
        // Revoke the token that was used to authenticate the current request
        // $user->currentAccessToken()->delete();
        $user->tokens()->delete();
    }

    public function createTokenUser($user) {
        $tokenName = $user->email . $user->uuid . $user->password;

        $token = $user->createToken($tokenName)->plainTextToken;

        return $token;
    }
}