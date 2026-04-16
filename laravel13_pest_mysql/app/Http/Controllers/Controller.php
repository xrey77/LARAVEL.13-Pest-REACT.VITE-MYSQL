<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

 /**
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Enter your Sanctum token"
 * )
 */
#[OA\Info(title: "Apple Inc. API Management", version: "1.0.0")]
abstract class Controller
{
    //
}
