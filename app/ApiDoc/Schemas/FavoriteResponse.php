<?php

namespace App\ApiDoc\Schemas;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="FavoriteResponse",
 *     type="object",
 *     @OA\Property(
 *         property="data",
 *         type="array",
 *         @OA\Items(
 *             type="object",
 *             @OA\Property(property="id", type="integer", example=1),
 *             @OA\Property(property="user_id", type="integer", example=12),
 *             @OA\Property(property="city_name", type="string", example="Yaguaraparo"),
 *             @OA\Property(property="added_at", type="string", format="date-time", example="2025-05-20T23:30:24+00:00")
 *         )
 *     )
 * )
 */
class FavoriteResponse {}
