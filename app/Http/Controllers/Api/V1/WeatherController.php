<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\GetWeatherRequest;
use App\Http\Resources\WeatherResource;
use App\Services\SearchHistoryService;
use App\Services\WeatherApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class WeatherController extends Controller
{
    protected WeatherApiService $weatherApiService;
    protected SearchHistoryService $searchHistoryService;

    public function __construct(WeatherApiService $weatherApiService, SearchHistoryService $searchHistoryService)
    {
        $this->weatherApiService = $weatherApiService;
        $this->searchHistoryService = $searchHistoryService;
    }

    /**
     * Handle the incoming request.
     */
    public function __invoke(GetWeatherRequest $request): JsonResponse|WeatherResource
    {
        $city = $request->validated()['city'];
        $user = $request->user();

        try {
            $weatherData = $this->weatherApiService->fetchWeatherForCity($city);

            if (!$weatherData) {
                return response()->json([
                    'message' => __("Weather data not found for city: {$city}. It might be an invalid city name or an API issue."),
                ], Response::HTTP_NOT_FOUND);
            }
            if ($user) {
                $this->searchHistoryService->logSearch($user, $city, $weatherData);
            }

            return new WeatherResource($weatherData);
        } catch (\Exception $e) {
            Log::error("Error in WeatherController for city {$city}: " . $e->getMessage());
            return response()->json([
                'message' => __('An error occurred while fetching weather data.'),
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
