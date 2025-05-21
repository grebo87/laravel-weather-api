<?php

namespace App\ApiDoc\Schemas;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="InternalServerError",
 *     type="object",
 *     @OA\Property(
 *         property="message",
 *         type="string",
 *         example="Server Error"
 *     ),
 * )
 */

class InternalServerError {
    // This class is only used for Swagger documentation
}
