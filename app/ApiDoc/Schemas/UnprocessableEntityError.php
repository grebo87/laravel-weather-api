<?php

namespace App\ApiDoc\Schemas;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="UnprocessableEntityError",
 *     type="object",
 *     @OA\Property(
 *         property="message",
 *         type="string",
 *         example="message error example"
 *     )
 * )
 */

class UnprocessableEntityError
{
}