<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WeatherResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // $this->resource es el array de datos que viene del WeatherApiService
        if (empty($this->resource) || !isset($this->resource['location']) || !isset($this->resource['current'])) {
            return [
                'error' => __('Weather data is incomplete or unavailable.'),
            ];
        }

        return [
            'location' => [
                'name' => $this->resource['location']['name'] ?? null,
                'region' => $this->resource['location']['region'] ?? null,
                'country' => $this->resource['location']['country'] ?? null,
                'localtime' => $this->resource['location']['localtime'] ?? null,
                'lat' => $this->resource['location']['lat'] ?? null,
                'lon' => $this->resource['location']['lon'] ?? null,
            ],
            'current_weather' => [
                'temperature_celsius' => $this->resource['current']['temp_c'] ?? null,
                'temperature_fahrenheit' => $this->resource['current']['temp_f'] ?? null,
                'condition_text' => $this->resource['current']['condition']['text'] ?? null,
                'condition_icon' => 'https:' . ($this->resource['current']['condition']['icon'] ?? null), // WeatherAPI devuelve URL sin https:
                'wind_kph' => $this->resource['current']['wind_kph'] ?? null,
                'wind_mph' => $this->resource['current']['wind_mph'] ?? null,
                'wind_direction' => $this->resource['current']['wind_dir'] ?? null,
                'pressure_mb' => $this->resource['current']['pressure_mb'] ?? null,
                'humidity_percent' => $this->resource['current']['humidity'] ?? null,
                'cloud_cover_percent' => $this->resource['current']['cloud'] ?? null,
                'feels_like_celsius' => $this->resource['current']['feelslike_c'] ?? null,
                'visibility_km' => $this->resource['current']['vis_km'] ?? null,
                'uv_index' => $this->resource['current']['uv'] ?? null,
                'gust_kph' => $this->resource['current']['gust_kph'] ?? null,
                'last_updated' => $this->resource['current']['last_updated'] ?? null,
            ],
        ];
    }
}