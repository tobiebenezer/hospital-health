<?php

namespace App\OpenApi;

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Hospital API",
 *     description="API documentation for the Hospital application"
 * )
 *
 * @OA\Server(
 *     url="/",
 *     description="Default Server"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="http",
 *     scheme="bearer",
 *     description="Enter token in format (Bearer <token>)",
 *     name="Authorization",
 *     in="header"
 * )
 */
class OpenApi {}
