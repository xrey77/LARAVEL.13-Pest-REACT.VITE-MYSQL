<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

 #[oa\SecurityScheme(
    securityScheme: 'sanctum',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'JWT',
    description: 'Enter your Sanctum token'
)]
#[OA\Info(title: "Apple Inc. API Management", version: "1.0.0")]
#[OA\Tag(name: "Users", description: "Operations related to users")]
#[OA\Tag(name: "Products", description: "Operations related to products")]
#[OA\Tag(name: "Authentications", description: "Login operations")] 
abstract class Controller
{
    //
}
