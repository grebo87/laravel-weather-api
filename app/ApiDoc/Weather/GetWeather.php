<?php

namespace App\ApiDoc\Weather;

use App\ApiDoc\ApiDoc;
use OpenApi\Annotations as OA;

/**
 * @OA\Get(
 *     path="/api/v1/weather",
 *     operationId="getWeatherByCity",
 *     tags={"Weather"},
 *     summary="Get weather for a city",
 *     description="Returns current weather information for the specified city",
 *     security={{"sanctum": {}}},
 *     @OA\Parameter(
 *         name="city",
 *         in="query",
 *         required=true,
 *         description="City name",
 *         @OA\Schema(type="string"),
 *         example="Yaguaraparo"
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successful operation",
 *         @OA\JsonContent(
 *             ref="#/components/schemas/WeatherResponse"
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Bad Request - Missing or invalid city parameter",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="The city field is required.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthenticated",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Unauthenticated.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="City not found",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Weather data not found for the specified city. It might be an invalid city name or an API issue."
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal Server Error",
 *         @OA\JsonContent(ref="#/components/schemas/InternalServerError")
 *     )
 * )
 */
class GetWeather extends ApiDoc
{
    // This class is only used for Swagger documentation
}
