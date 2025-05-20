<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreFavoriteCityRequest;
use App\Http\Resources\FavoriteCityResource;
use App\Services\FavoriteCityService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Validation\ValidationException;

class FavoriteCityController extends Controller
{
    protected FavoriteCityService $favoriteCityService;

    public function __construct(FavoriteCityService $favoriteCityService)
    {
        $this->favoriteCityService = $favoriteCityService;
    }

    /**
     * Display a listing of the user's favorite cities.
     *
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $favorites = $this->favoriteCityService->getUserFavorites($request->user());
        return FavoriteCityResource::collection($favorites);
    }

    /**
     * Store a newly created favorite city in storage.
     *
     * @param StoreFavoriteCityRequest $request
     * @return JsonResponse|FavoriteCityResource
     */
    public function store(StoreFavoriteCityRequest $request)
    {
        $user = $request->user();
        $cityName = $request->validated()['city_name'];

        try {
            $favorite = $this->favoriteCityService->addFavorite($user, $cityName);
            return new FavoriteCityResource($favorite);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            Log::error("Error storing favorite city: " . $e->getMessage());
            return response()->json([
                'message' => 'Could not add city to favorites.',
                'error' => $e->getMessage() // Para desarrollo
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified favorite city from storage.
     *
     * @param Request $request
     * @param int $favoriteCityId
     *
     * @return JsonResponse
     */
    public function destroy(Request $request, int $favoriteCityId): JsonResponse
    {
        $user = $request->user();
        $deleted = $this->favoriteCityService->removeFavorite($user, $favoriteCityId);

        if ($deleted) {
            return response()->json(null, Response::HTTP_NO_CONTENT);
        }

        return response()->json([
            'message' => __('Favorite city not found or you do not have permission to delete it.'),
        ], Response::HTTP_NOT_FOUND);
    }
}
