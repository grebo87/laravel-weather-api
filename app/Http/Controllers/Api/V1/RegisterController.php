<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Symfony\Component\HttpFoundation\Response;

class RegisterController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Handle the incoming RegisterRequest.
     */
    public function __invoke(RegisterRequest $request)
    {
        $attributes = $request->validated();
        $user = $this->authService->registerUser($attributes);

        $loginData = $this->authService->loginUser([
            'email' => $user->email,
            'password' => $request->input('password')
        ]);

        return response()->json([
            'message' => __('User registered successfully.'),
            'user' => new UserResource($loginData['user']),
            'token_type' => 'Bearer',
            'access_token' => $loginData['token'],
        ],  Response::HTTP_CREATED);
    }
}
