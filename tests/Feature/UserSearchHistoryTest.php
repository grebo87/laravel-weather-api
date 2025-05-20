<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\SearchHistory;

class UserSearchHistoryTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_authenticated_user_can_list_their_search_history(): void
    {
        SearchHistory::factory()->count(5)->create(['user_id' => $this->user->id]);
        $otherUser = User::factory()->create();
        SearchHistory::factory()->count(2)->create(['user_id' => $otherUser->id]); // Search history from another user

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson(route('api.history.index'));

        $response->assertStatus(200)
            ->assertJsonCount(5, 'data') // Ensures it only lists the authenticated user's history
            ->assertJsonStructure(['data' => [['id', 'city_name', 'weather_summary', 'searched_at']]]);
    }

    public function test_search_history_can_be_limited(): void
    {
        SearchHistory::factory()->count(15)->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson(route('api.history.index', ['limit' => 7]));

        $response->assertStatus(200)
            ->assertJsonCount(7, 'data');
    }

    public function test_search_history_is_ordered_by_most_recent(): void
    {
        SearchHistory::factory()->create([
            'user_id' => $this->user->id,
            'city_name' => 'OldCity',
            'searched_at' => now()->subDay()
        ]);
        $recentSearch = SearchHistory::factory()->create([
            'user_id' => $this->user->id,
            'city_name' => 'NewCity',
            'searched_at' => now()
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson(route('api.history.index'));

        $response->assertStatus(200)
            ->assertJsonPath('data.0.city_name', $recentSearch->city_name);
    }

    public function test_unauthenticated_user_cannot_list_search_history(): void
    {
        $response = $this->getJson(route('api.history.index'));
        $response->assertStatus(401);
    }
}
