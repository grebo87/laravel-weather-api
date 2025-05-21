<?php

namespace App\ApiDoc\Schemas;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="WeatherResponse",
 *     type="object",
 *     @OA\Property(
 *         property="data",
 *         type="object",
 *         @OA\Property(
 *             property="location",
 *             type="object",
 *             @OA\Property(property="name", type="string", example="Yaguaraparo"),
 *             @OA\Property(property="region", type="string", example="Sucre"),
 *             @OA\Property(property="country", type="string", example="Venezuela"),
 *             @OA\Property(property="localtime", type="string", example="2025-05-20 17:27"),
 *             @OA\Property(property="lat", type="number", format="float", example=10.5689),
 *             @OA\Property(property="lon", type="number", format="float", example=-62.8239)
 *         ),
 *         @OA\Property(
 *             property="current_weather",
 *             type="object",
 *             @OA\Property(property="temperature_celsius", type="number", format="float", example=30.1),
 *             @OA\Property(property="temperature_fahrenheit", type="number", format="float", example=86.2),
 *             @OA\Property(property="condition_text", type="string", example="Patchy rain nearby"),
 *             @OA\Property(property="condition_icon", type="string", format="uri", example="https://cdn.weatherapi.com/weather/64x64/day/176.png"),
 *             @OA\Property(property="wind_kph", type="number", format="float", example=15.5),
 *             @OA\Property(property="wind_mph", type="number", format="float", example=9.6),
 *             @OA\Property(property="wind_direction", type="string", example="E"),
 *             @OA\Property(property="pressure_mb", type="integer", example=1011),
 *             @OA\Property(property="humidity_percent", type="integer", example=100),
 *             @OA\Property(property="cloud_cover_percent", type="integer", example=0),
 *             @OA\Property(property="feels_like_celsius", type="number", format="float", example=36.4),
 *             @OA\Property(property="visibility_km", type="integer", example=9),
 *             @OA\Property(property="uv_index", type="number", format="float", example=0.9),
 *             @OA\Property(property="gust_kph", type="integer", example=22),
 *             @OA\Property(property="last_updated", type="string", example="2025-05-20 17:15")
 *         )
 *     )
 * )
 */
class WeatherResponse {}
