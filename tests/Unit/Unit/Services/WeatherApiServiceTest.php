<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\WeatherApiService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class WeatherApiServiceTest extends TestCase
{
    protected WeatherApiService $weatherApiService;

    protected function setUp(): void
    {
        parent::setUp();
        // Configure the API key for tests, or mock config()
        config(['services.weatherapi.key' => 'test_api_key']);
        config(['services.weatherapi.url' => 'http://fakeapi.com/weather']);
        config(['cache.weather_ttl' => 300]);

        $this->weatherApiService = new WeatherApiService();
    }

    public function test_fetch_weather_for_city_returns_data_from_api_and_caches_it(): void
    {
        $cityName = 'Yaguaraparo';
        $fakeApiResponse = ['location' => ['name' => $cityName], 'current' => ['temp_c' => 10]];

        Http::fake([
            config('services.weatherapi.url') . '*' => Http::response($fakeApiResponse, 200),
        ]);

        // Simulates that it's not in cache
        Cache::shouldReceive('get')
            ->once()
            ->with("weather_data_{$cityName}")
            ->andReturn(null);

        Cache::shouldReceive('put')
            ->once()
            ->with("weather_data_{$cityName}", $fakeApiResponse, 300)
            ->andReturn(true);

        $result = $this->weatherApiService->fetchWeatherForCity($cityName);

        $this->assertEquals($fakeApiResponse, $result);
        Http::assertSentCount(1); // Ensures the API was called
    }

    public function test_fetch_weather_for_city_returns_data_from_cache_if_available(): void
    {
        $cityName = 'Carupano';
        $cachedData = ['location' => ['name' => $cityName], 'current' => ['temp_c' => 12]];

        // Simula que está en caché
        Cache::shouldReceive('get')
            ->once()
            ->with("weather_data_{$cityName}")
            ->andReturn($cachedData);

        Http::fake(['*' => Http::response('Should not be called', 500)]);


        $result = $this->weatherApiService->fetchWeatherForCity($cityName);

        $this->assertEquals($cachedData, $result);
        Http::assertNothingSent(); // Ensures the API was NOT called
        Cache::shouldReceive('put')->never(); // Should not attempt to cache again
    }

    public function test_fetch_weather_returns_null_on_api_failure_or_city_not_found(): void
    {
        $cityName = 'UnknownCity';

        Http::fake([
            config('services.weatherapi.url') . '*' => Http::response(['error' => ['message' => 'No matching location found.']], 400),
        ]);

        Cache::shouldReceive('get')->once()->with("weather_data_{$cityName}")->andReturn(null);

        $result = $this->weatherApiService->fetchWeatherForCity($cityName);

        $this->assertNull($result);
    }

    public function test_fetch_weather_throws_exception_if_api_key_is_missing(): void
    {
        config(['services.weatherapi.key' => null]); // Simulates missing API key
        $this->weatherApiService = new WeatherApiService(); // Re-instantiate with the new config

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("WeatherAPI key is not configured. Cannot fetch weather data.");

        $this->weatherApiService->fetchWeatherForCity('Caracas');
    }

    public function test_fetch_weather_handles_connection_exception(): void
    {
        $cityName = 'Los Teques';

        Http::fake(function ($request) { // Simular una ConnectionException
            throw new \Illuminate\Http\Client\ConnectionException("Connection timed out");
        });

        Cache::shouldReceive('get')->once()->with("weather_data_{$cityName}")->andReturn(null);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Could not connect to the weather service. Please try again later.");

        $this->weatherApiService->fetchWeatherForCity($cityName);
    }
}
