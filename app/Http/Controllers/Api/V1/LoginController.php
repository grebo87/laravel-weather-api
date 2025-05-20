<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\SignInRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class LoginController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Handle the incoming SignInRequest.
     */
    public function __invoke(SignInRequest $request)
    {
        try {
            $loginData = $this->authService->loginUser($request->validated());

            return response()->json([
                'message' => __('Login successful.'),
                'user' => new UserResource($loginData['user']),
                'token_type' => 'Bearer',
                'access_token' => $loginData['token']
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => __('Invalid credentials.'),
                'errors' => $e->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            return response()->json([
                'message' => __('Login failed.'),
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
