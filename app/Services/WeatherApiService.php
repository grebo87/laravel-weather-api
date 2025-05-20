<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Exception;

class WeatherApiService
{
    protected string|null $apiKey;
    protected string $baseUrl;
    protected int $cacheDuration;

    public function __construct()
    {
        $this->apiKey = config('services.weatherapi.key');
        $this->baseUrl = config('services.weatherapi.url');
        $this->cacheDuration = config('cache.weather_ttl', 300);

        if (empty($this->apiKey)) {
            Log::error('WeatherAPI key is not configured.');
        }
    }

    /**
     * Fetch weather data for a given city.
     *
     * @param string $city
     * @return array|null
     * @throws Exception
     */
    public function fetchWeatherForCity(string $city): ?array
    {
        if (empty($this->apiKey)) {
            throw new Exception(__("WeatherAPI key is not configured. Cannot fetch weather data."));
        }

        $cacheKey = "weather_data_{$city}";

        // Intenta obtener desde el cache primero
        $cachedData = Cache::get($cacheKey);
        if ($cachedData) {
            Log::info(__("Weather data for {$city} found in cache."));
            return $cachedData;
        }

        Log::info(__("Fetching weather data for {$city} from API."));
        try {
            $response = Http::timeout(10) // Timeout de 10 segundos
                ->get($this->baseUrl, [
                    'key' => $this->apiKey,
                    'q' => $city,
                    'aqi' => 'no' // Air Quality Data (no/yes)
                ]);

            if ($response->failed()) {
                // Log the specific API error if possible
                $apiError = $response->json('error.message', 'Unknown API error');
                Log::error("WeatherAPI request failed for city {$city}. Status: {$response->status()}. Error: {$apiError}");
                return null;
            }

            $weatherData = $response->json();

            if (empty($weatherData) || isset($weatherData['error'])) {
                $apiError = $weatherData['error']['message'] ?? __('City not found or invalid request');
                Log::warning("WeatherAPI returned an error for city {$city}: {$apiError}");
                return null;
            }

            // Store in cache
            Cache::put($cacheKey, $weatherData, $this->cacheDuration);
            Log::info("Weather data for {$city} fetched and cached.");

            return $weatherData;
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error("Connection error while fetching weather for {$city}: " . $e->getMessage());
            throw new Exception(__("Could not connect to the weather service. Please try again later."), 0, $e);
        } catch (\Exception $e) {
            Log::error("General error while fetching weather for {$city}: " . $e->getMessage());
            throw new Exception(__("An error occurred while fetching weather data. Please try again later."), 0, $e);
        }
    }
}
