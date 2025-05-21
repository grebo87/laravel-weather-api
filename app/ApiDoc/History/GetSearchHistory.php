<?php

namespace App\ApiDoc\History;

use App\ApiDoc\ApiDoc;
use OpenApi\Annotations as OA;

/**
 * @OA\Get(
 *     path="/api/v1/history",
 *     operationId="getSearchHistory",
 *     tags={"History"},
 *     summary="Get user's weather search history",
 *     description="Returns a list of weather searches made by the authenticated user",
 *     security={{"sanctum": {}}},
 *     @OA\Parameter(
 *         name="limit",
 *         in="query",
 *         description="Number of history items to return (default: 10)",
 *         required=false,
 *         @OA\Schema(type="integer", default=10, minimum=1, maximum=100)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Search history retrieved successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     type="object",
 *                     @OA\Property(property="id", type="integer", example=5),
 *                     @OA\Property(property="user_id", type="integer", example=12),
 *                     @OA\Property(property="city_name", type="string", example="Yaguaraparo"),
 *                     @OA\Property(
 *                         property="weather_summary",
 *                         type="object",
 *                         @OA\Property(property="temp_c", type="number", format="float", example=28.1),
 *                         @OA\Property(property="humidity", type="integer", example=100),
 *                         @OA\Property(property="wind_kph", type="number", format="float", example=13.7),
 *                         @OA\Property(property="localtime", type="string", example="2025-05-20 19:22"),
 *                         @OA\Property(property="condition_text", type="string", example="Patchy rain nearby")
 *                     ),
 *                     @OA\Property(property="searched_at", type="string", format="date-time", example="2025-05-20T23:23:13+00:00")
 *                 )
 *             )
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
 *         response=500,
 *         description="Internal Server Error",
 *         @OA\JsonContent(ref="#/components/schemas/InternalServerError")
 *     )
 * )
 */
class GetSearchHistory  extends ApiDoc{}
