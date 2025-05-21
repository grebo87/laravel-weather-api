<?php

namespace App\ApiDoc\Favorites;

use App\ApiDoc\ApiDoc;
use OpenApi\Annotations as OA;

/**
 * @OA\Post(
 *     path="/api/v1/favorites",
 *     operationId="addFavoriteCity",
 *     tags={"Favorites"},
 *     summary="Add a city to user's favorites",
 *     description="Adds a city to the authenticated user's list of favorite cities",
 *     security={{"sanctum": {}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"city_name"},
 *             @OA\Property(property="city_name", type="string", example="carupano")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="City added to favorites successfully",
 *         @OA\JsonContent(
 *             ref="#/components/schemas/FavoriteResponse"
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
 *         response=422,
 *         description="Validation error",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="The given data was invalid."),
 *             @OA\Property(
 *                 property="errors",
 *                 type="object",
 *                 @OA\Property(
 *                     property="city_name",
 *                     type="array",
 *                     @OA\Items(type="string", example="The city name field is required.")
 *                 )
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
class AddFavorite  extends ApiDoc{}
