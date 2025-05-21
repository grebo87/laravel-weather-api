<?php

namespace App\ApiDoc\User;

use App\ApiDoc\ApiDoc;
use OpenApi\Annotations as OA;

/**
 * @OA\Get(
 *     path="/api/v1/user",
 *     operationId="getUserData",
 *     tags={"User"},
 *     summary="Get authenticated user's data",
 *     description="Returns the authenticated user's profile information",
 *     security={{"sanctum": {}}},
 *     @OA\Response(
 *         response=200,
 *         description="User data retrieved successfully",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=42),
 *                 @OA\Property(property="first_name", type="string", example="John"),
 *                 @OA\Property(property="last_name", type="string", example="Doe"),
 *                 @OA\Property(property="email", type="string", format="email", example="john.doe@example.com"),
 *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-01-15T10:30:00+00:00"),
 *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-05-01T14:22:15+00:00")
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
class GetUser  extends ApiDoc{}
