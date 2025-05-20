<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class WeatherTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_authenticated_user_can_get_weather_data(): void
    {
        $cityName = 'London';
        $fakeWeatherData = [ // Simplified structure of the WeatherAPI response
            'location' => ['name' => $cityName, 'region' => 'City of London, Greater London', 'country' => 'United Kingdom', 'localtime' => '2023-10-27 10:00'],
            'current' => ['temp_c' => 15.0, 'condition' => ['text' => 'Partly cloudy'], 'wind_kph' => 10.0, 'humidity' => 70],
        ];

        Http::fake([ // Mock the external API response
            config('services.weatherapi.url') . '*' => Http::response($fakeWeatherData, 200),
        ]);

        Cache::shouldReceive('get') // Mockea Cache::get
            ->once()
            ->with("weather_data_{$cityName}")
            ->andReturn(null); // Simulate that data is not in cache the first time

        Cache::shouldReceive('put') // Mockea Cache::put
            ->once()
            ->with("weather_data_{$cityName}", $fakeWeatherData, config('cache.weather_ttl', 300));


        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson(route('api.weather.get', ['city' => $cityName]));

        $response->assertStatus(200)
            ->assertJsonStructure(['data' => [
                'location' => ['name', 'localtime'],
                'current_weather' => ['temperature_celsius', 'condition_text', 'wind_kph', 'humidity_percent'],
            ]])
            ->assertJsonPath('data.location.name', $cityName);

        // Verify that it was saved in the search history
        $this->assertDatabaseHas('search_histories', [
            'user_id' => $this->user->id,
            'city_name' => $cityName,
        ]);
    }

    public function test_weather_data_is_retrieved_from_cache_if_available(): void
    {
        $cityName = 'Paris';
        $cachedWeatherData = [
            'location' => ['name' => $cityName, /* ... */],
            'current' => ['temp_c' => 18.0, /* ... */],
        ];

        // Simulate that data is in cache
        Cache::shouldReceive('get')
            ->once()
            ->with("weather_data_{$cityName}")
            ->andReturn($cachedWeatherData);

        // Http::fake should be present, but shouldn't be called if the cache works
        Http::fake([config('services.weatherapi.url') . '*' => Http::response([], 500)]); // Error response if called

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson(route('api.weather.get', ['city' => $cityName]));

        $response->assertStatus(200)
            ->assertJsonPath('data.location.name', $cityName);

        Http::assertNothingSent(); // Verifies that no external HTTP calls were made
    }

    public function test_get_weather_requires_city_parameter(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson(route('api.weather.get')); // Without the city parameter

        $response->assertStatus(422) // HTTP_UNPROCESSABLE_ENTITY
            ->assertJsonValidationErrors(['city']);
    }

    public function test_get_weather_returns_not_found_for_invalid_city(): void
    {
        $invalidCityName = 'InvalidCityName123';

        Http::fake([
            config('services.weatherapi.url') . '*' => Http::response(['error' => ['message' => 'No matching location found.']], 400), // WeatherAPI returns 400 for city not found
        ]);

        Cache::shouldReceive('get')->andReturn(null); // No en cache

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson(route('api.weather.get', ['city' => $invalidCityName]));

        $response->assertStatus(404) // HTTP_NOT_FOUND
            ->assertJsonPath('message', "Weather data not found for city: {$invalidCityName}. It might be an invalid city name or an API issue.");
    }

    public function test_unauthenticated_user_cannot_get_weather_data(): void
    {
        $response = $this->getJson(route('api.weather.get', ['city' => 'London']));
        $response->assertStatus(401); // HTTP_UNAUTHORIZED
    }
}
