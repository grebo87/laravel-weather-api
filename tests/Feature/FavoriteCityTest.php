<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\FavoriteCity;

class FavoriteCityTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_authenticated_user_can_add_a_favorite_city(): void
    {
        $cityName = $this->faker->city;

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson(route('api.favorites.store'), ['city_name' => $cityName]);

        $response->assertStatus(201)
            ->assertJsonStructure(['data' => ['id', 'user_id', 'city_name', 'added_at']])
            ->assertJsonPath('data.city_name', $cityName)
            ->assertJsonPath('data.user_id', $this->user->id);

        $this->assertDatabaseHas('favorite_cities', [
            'user_id' => $this->user->id,
            'city_name' => $cityName,
        ]);
    }

    public function test_adding_favorite_city_requires_city_name(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson(route('api.favorites.store'), []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['city_name']);
    }

    public function test_user_cannot_add_same_city_twice_as_favorite(): void
    {
        $cityName = $this->faker->city;
        FavoriteCity::factory()->create(['user_id' => $this->user->id, 'city_name' => $cityName]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson(route('api.favorites.store'), ['city_name' => $cityName]);

        $response->assertStatus(422) // UNPROCESSABLE_ENTITY
            ->assertJsonPath('errors.city_name.0', "'{$cityName}' is already in your favorites.");
    }


    public function test_authenticated_user_can_list_their_favorite_cities(): void
    {
        FavoriteCity::factory()->count(3)->create(['user_id' => $this->user->id]);

        $otherUser = User::factory()->create();
        FavoriteCity::factory()->create(['user_id' => $otherUser->id]); // A favorite from another user

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson(route('api.favorites.index'));

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data') // Ensures only the user's favorites are listed
            ->assertJsonStructure(['data' => [['id', 'city_name']]]);
    }

    public function test_authenticated_user_can_delete_their_favorite_city(): void
    {
        $favoriteCity = FavoriteCity::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson(route('api.favorites.destroy', ['favoriteCity' => $favoriteCity->id]));

        $response->assertStatus(204); // NO_CONTENT

        $this->assertDatabaseMissing('favorite_cities', ['id' => $favoriteCity->id]);
    }

    public function test_user_cannot_delete_another_users_favorite_city(): void
    {
        $otherUser = User::factory()->create();
        $otherUserFavorite = FavoriteCity::factory()->create(['user_id' => $otherUser->id]); // Belongs to another user

        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson(route('api.favorites.destroy', ['favoriteCity' => $otherUserFavorite->id]));

        $response->assertStatus(404); // NOT_FOUND

        $this->assertDatabaseHas('favorite_cities', ['id' => $otherUserFavorite->id]);
    }

    public function test_unauthenticated_user_cannot_access_favorites_endpoints(): void
    {
        $this->getJson(route('api.favorites.index'))->assertStatus(401);
        $this->postJson(route('api.favorites.store'), ['city_name' => 'TestCity'])->assertStatus(401);
        $this->deleteJson(route('api.favorites.destroy', ['favoriteCity' => 1]))->assertStatus(401);
    }
}
