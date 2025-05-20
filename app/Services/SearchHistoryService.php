<?php

namespace App\Services;

use App\Models\SearchHistory;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class SearchHistoryService
{
    /**
     * Log a weather search in the user's search history.
     *
     * @param User $user The user who performed the search
     * @param string $cityName The name of the city that was searched
     * @param array $weatherData The weather data retrieved from the API
     * @return SearchHistory|null Returns the created search history record or null on failure
     */
    public function logSearch(User $user, string $cityName, array $weatherData): ?SearchHistory
    {
        try {
            return SearchHistory::create([
                'user_id' => $user->id,
                'city_name' => $cityName,
                'weather_data' => $this->prepareWeatherDataForHistory($weatherData),
                'searched_at' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error("Error logging search history for user {$user->id} and city {$cityName}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Prepare and extract relevant weather data for storage in search history.
     *
     * @param array $weatherData The complete weather data array from the weather API
     * @return array A simplified array containing only the weather data we want to store
     */
    private function prepareWeatherDataForHistory(array $weatherData): array
    {
        return [
            'temp_c' => $weatherData['current']['temp_c'] ?? null,
            'condition_text' => $weatherData['current']['condition']['text'] ?? null,
            'wind_kph' => $weatherData['current']['wind_kph'] ?? null,
            'humidity' => $weatherData['current']['humidity'] ?? null,
            'localtime' => $weatherData['location']['localtime'] ?? null,
        ];
    }

    /**
     * Retrieve the most recent search history records for a user.
     *
     * @param User $user The user whose search history to retrieve
     * @param int $limit Maximum number of records to return (default: 10)
     * @return \Illuminate\Database\Eloquent\Collection Collection of SearchHistory models
     */
    public function getRecentSearches(User $user, int $limit = 10)
    {
        return SearchHistory::where('user_id', $user->id)
            ->orderBy('searched_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
