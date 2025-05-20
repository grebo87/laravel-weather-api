<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SearchHistory>
 */
class SearchHistoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => fn() => \App\Models\User::factory(),
            'city_name' => fake()->city(),
            'weather_data' => [
                'temp_c' => fake()->numberBetween(0, 40),
                'condition_text' => fake()->randomElement(['Sunny', 'Cloudy', 'Rainy', 'Partly cloudy', 'Clear']),
                'wind_kph' => fake()->randomFloat(1, 0, 30),
                'humidity' => fake()->numberBetween(30, 100),
                'localtime' => fake()->dateTimeThisMonth()->format('Y-m-d H:i'),
            ],
            'searched_at' => now(),
        ];
    }
}
