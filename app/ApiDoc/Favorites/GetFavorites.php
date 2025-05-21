<?php

namespace App\ApiDoc\Favorites;

use App\ApiDoc\ApiDoc;
use OpenApi\Annotations as OA;

/**
 * @OA\Get(
 *     path="/api/v1/favorites",
 *     operationId="getFavoriteCities",
 *     tags={"Favorites"},
 *     summary="Get user's favorite cities",
 *     description="Returns a list of the authenticated user's favorite cities",
 *     security={{"sanctum": {}}},
 *     @OA\Response(
 *         response=200,
 *         description="List of favorite cities retrieved successfully",
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
 *         response=500,
 *         description="Internal Server Error",
 *         @OA\JsonContent(ref="#/components/schemas/InternalServerError")
 *     )
 * )
 */
class GetFavorites extends ApiDoc
{
    // This class is only used for Swagger documentation
}
