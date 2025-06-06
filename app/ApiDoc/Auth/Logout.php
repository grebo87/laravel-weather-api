<?php

namespace App\ApiDoc\Auth;

use App\ApiDoc\ApiDoc;
use OpenApi\Annotations as OA;

/**
 *
 * @OA\Post(
 *     path="/api/v1/logout",
 *     operationId="logout",
 *     tags={"Auth"},
 *     summary="logout user",
 *     description="Logout endpoint for user",
 *     security={ {"sanctum": {} }},
 *     @OA\Response(
 *       response="204",
 *       description="Logout Successful",
 *       @OA\JsonContent()
 *     ),
 *     @OA\Response(
 *         response="500",
 *         description="Internal Server Error",
 *         @OA\JsonContent(ref="#/components/schemas/InternalServerError"),
 *     ),
 * )
 *
 */
class Logout extends ApiDoc
{
}
