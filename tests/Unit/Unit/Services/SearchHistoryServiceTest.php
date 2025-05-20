<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\SearchHistoryService;
use App\Models\User;
use App\Models\SearchHistory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;

class SearchHistoryServiceTest extends TestCase
{
    use RefreshDatabase;

    protected SearchHistoryService $searchHistoryService;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->searchHistoryService = new SearchHistoryService();
        $this->user = User::factory()->create();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }


    public function test_log_search_creates_history_record(): void
    {
        $cityName = 'Tokyo';
        $weatherData = [ // Weather data structure expected by prepareWeatherDataForHistory
            'current' => ['temp_c' => 25, 'condition' => ['text' => 'Sunny'], 'wind_kph' => 5, 'humidity' => 60],
            'location' => ['localtime' => now()->toDateTimeString()]
        ];

        $history = $this->searchHistoryService->logSearch($this->user, $cityName, $weatherData);

        $this->assertInstanceOf(SearchHistory::class, $history);
        $this->assertEquals($cityName, $history->city_name);
        $this->assertEquals($this->user->id, $history->user_id);
        $this->assertIsArray($history->weather_data);
        $this->assertEquals(25, $history->weather_data['temp_c']);
        $this->assertDatabaseHas('search_histories', ['user_id' => $this->user->id, 'city_name' => $cityName]);
    }

    public function test_get_recent_searches_returns_limited_and_ordered_history(): void
    {
        SearchHistory::factory()->create(['user_id' => $this->user->id, 'searched_at' => now()->subDays(2)]);
        $mostRecent = SearchHistory::factory()->create(['user_id' => $this->user->id, 'searched_at' => now()]);
        SearchHistory::factory()->create(['user_id' => $this->user->id, 'searched_at' => now()->subDay()]);
        SearchHistory::factory()->create(); // Another user

        $history = $this->searchHistoryService->getRecentSearches($this->user, 2);

        $this->assertCount(2, $history);
        $this->assertEquals($mostRecent->id, $history->first()->id); // Checks the order
    }
}
