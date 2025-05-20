<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class LogoutController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request) : JsonResponse
    {
        try {
            $this->authService->logoutUser($request->user());
            return response()->json(['message' => __('Successfully logged out')], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => __('Logout failed.'),
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
