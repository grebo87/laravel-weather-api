<?php

namespace App\ApiDoc\Favorites;

use App\ApiDoc\ApiDoc;
use OpenApi\Annotations as OA;

/**
 * @OA\Delete(
 *     path="/api/v1/favorites/{id}",
 *     operationId="deleteFavoriteCity",
 *     tags={"Favorites"},
 *     summary="Remove a city from user's favorites",
 *     description="Removes a city from the authenticated user's list of favorite cities",
 *     security={{"sanctum": {}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID of the favorite to remove",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=204,
 *         description="City removed from favorites successfully",
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthenticated",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Unauthenticated.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Forbidden - User doesn't own the favorite",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="You don't have permission to delete this favorite.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Favorite not found",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Favorite not found.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal Server Error",
 *         @OA\JsonContent(ref="#/components/schemas/InternalServerError")
 *     )
 * )
 */
class DeleteFavorite  extends ApiDoc{}
