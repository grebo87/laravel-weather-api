<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\FavoriteCityService;
use App\Models\User;
use App\Models\FavoriteCity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;

class FavoriteCityServiceTest extends TestCase
{
    use RefreshDatabase;

    protected FavoriteCityService $favoriteCityService;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->favoriteCityService = new FavoriteCityService();
        $this->user = User::factory()->create();
    }

    public function test_add_favorite_creates_favorite_city(): void
    {
        $cityName = 'Yaguaraparo';
        $favorite = $this->favoriteCityService->addFavorite($this->user, $cityName);

        $this->assertInstanceOf(FavoriteCity::class, $favorite);
        $this->assertEquals($cityName, $favorite->city_name);
        $this->assertEquals($this->user->id, $favorite->user_id);
        $this->assertDatabaseHas('favorite_cities', [
            'user_id' => $this->user->id,
            'city_name' => $cityName,
        ]);
    }

    public function test_add_favorite_throws_validation_exception_if_already_exists(): void
    {
        $cityName = 'Carupano';
        FavoriteCity::factory()->create(['user_id' => $this->user->id, 'city_name' => $cityName]);

        $this->expectException(ValidationException::class);

        $this->favoriteCityService->addFavorite($this->user, $cityName);
    }

    public function test_get_user_favorites_returns_collection_of_favorites(): void
    {
        FavoriteCity::factory()->count(3)->create(['user_id' => $this->user->id]);

        // Another user
        $otherUser = User::factory()->create();
        FavoriteCity::factory()->create(['user_id' => $otherUser->id]);

        $favorites = $this->favoriteCityService->getUserFavorites($this->user);

        $this->assertCount(3, $favorites);
        foreach ($favorites as $favorite) {
            $this->assertEquals($this->user->id, $favorite->user_id);
        }
    }

    public function test_remove_favorite_deletes_city_and_returns_true(): void
    {
        $favorite = FavoriteCity::factory()->create(['user_id' => $this->user->id]);

        $result = $this->favoriteCityService->removeFavorite($this->user, $favorite->id);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('favorite_cities', ['id' => $favorite->id]);
    }

    public function test_remove_favorite_returns_false_if_not_found_or_not_owned(): void
    {
        // Another user
        $otherUser = User::factory()->create();
        $otherUserFavorite = FavoriteCity::factory()->create(['user_id' => $otherUser->id]);

        $resultNotFound = $this->favoriteCityService->removeFavorite($this->user, 999); // Non-existent ID
        $resultNotOwned = $this->favoriteCityService->removeFavorite($this->user, $otherUserFavorite->id);

        $this->assertFalse($resultNotFound);
        $this->assertFalse($resultNotOwned);
        $this->assertDatabaseHas('favorite_cities', ['id' => $otherUserFavorite->id]); // Ensures it wasn't deleted
    }

    public function test_is_favorite_returns_true_if_city_is_favorite(): void
    {
        $cityName = "Los Teques";
        FavoriteCity::factory()->create(['user_id' => $this->user->id, 'city_name' => $cityName]);

        $this->assertTrue($this->favoriteCityService->isFavorite($this->user, $cityName));
    }

    public function test_is_favorite_returns_false_if_city_is_not_favorite(): void
    {
        $this->assertFalse($this->favoriteCityService->isFavorite($this->user, "NonExistentFavorite"));
    }
}
