<?php

namespace App\ApiDoc\Schemas;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="LoginAndRegisterResponse",
 *     type="object",
 *     @OA\Property(
 *         property="access_token",
 *         type="string",
 *         example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."
 *     ),
 *     @OA\Property(
 *         property="token_type",
 *         type="string",
 *         example="bearer"
 *     ),
 *     @OA\Property(
 *         property="message",
 *         type="string",
 *         example="Login successful"
 *     ),
 *     @OA\Property(
 *         property="user",
 *         type="object",
 *         @OA\Property(property="id", type="integer", example=1),
 *         @OA\Property(property="first_name", type="string", example="John"),
 *         @OA\Property(property="last_name", type="string", example="Doe"),
 *         @OA\Property(property="email", type="string", format="email", example="user@example.com"),
 *         @OA\Property(property="email_verified_at", type="string", format="date-time", nullable=true, example="2025-05-20T18:50:54.000000Z"),
 *         @OA\Property(property="created_at", type="string", format="date-time", example="2025-05-20T18:50:54.000000Z"),
 *         @OA\Property(property="updated_at", type="string", format="date-time", example="2025-05-20T18:50:54.000000Z")
 *     )
 * )
 */
class LoginAndRegisterResponse
{
    // This class is only used for Swagger documentation
}
