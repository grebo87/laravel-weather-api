<?php

namespace App\Services;

use App\Models\User;
use App\Models\FavoriteCity;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class FavoriteCityService
{
    /**
     * Add a city to the user's favorites.
     *
     * @param User $user
     * @param string $cityName
     * @return FavoriteCity
     * @throws ValidationException
     */
    public function addFavorite(User $user, string $cityName): FavoriteCity
    {
        // Check if it already exists to avoid unique constraint error at service level
        $existingFavorite = $user->favoriteCities()->where('city_name', $cityName)->first();
        if ($existingFavorite) {
            throw ValidationException::withMessages([
                'city_name' => [__("'{$cityName}' is already in your favorites.")],
            ]);
        }

        try {
            return $user->favoriteCities()->create(['city_name' => $cityName]);
        } catch (\Illuminate\Database\QueryException $e) {
            // Specific capture for unique constraint error if the previous check fails for any reason (race condition)
            if ($e->errorInfo[1] == 1062 || str_contains(strtolower($e->getMessage()), 'unique constraint')) { // 1062 is the MySQL error code for duplicates
                Log::warning("Attempt to add duplicate favorite city '{$cityName}' for user {$user->id} despite check.");
                throw ValidationException::withMessages([
                    'city_name' => [__("'{$cityName}' is already in your favorites.")],
                ]);
            }
            Log::error("Error adding favorite city '{$cityName}' for user {$user->id}: " . $e->getMessage());
            throw $e; // Re-throw if it's another type of database error
        }
    }

    /**
     * Get all favorite cities for a user.
     *
     * @param User $user
     * @return Collection
     */
    public function getUserFavorites(User $user): Collection
    {
        return $user->favoriteCities()->orderBy('city_name')->get();
    }

    /**
     * Remove a city from the user's favorites.
     *
     * @param User $user
     * @param int $favoriteCityId
     * @return bool True if deleted, false if not found or not owned.
     */
    public function removeFavorite(User $user, int $favoriteCityId): bool
    {
        $favorite = $user->favoriteCities()->find($favoriteCityId);

        if ($favorite) {
            return $favorite->delete();
        }

        return false;
    }

    /**
     * Check if a city is a favorite for the user.
     *
     * @param User $user
     * @param string $cityName
     * @return bool
     */
    public function isFavorite(User $user, string $cityName): bool
    {
        return $user->favoriteCities()->where('city_name', $cityName)->exists();
    }
}
