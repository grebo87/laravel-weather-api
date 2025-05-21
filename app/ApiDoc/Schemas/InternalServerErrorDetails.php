<?php

namespace App\ApiDoc\Schemas;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="InternalServerErrorDetails",
 *     type="object",
 *     @OA\Property(
 *         property="status",
 *         type="string",
 *         example="500"
 *     ),
 *     @OA\Property(
 *         property="title",
 *         type="string",
 *         example="Internal Server Error"
 *     ),
 * )
 */

class InternalServerErrorDetails
{
}
