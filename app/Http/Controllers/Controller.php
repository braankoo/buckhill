<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="Test Api",
 *      description="Hello from testing API"
 * )
 *
 * @OA\Server(
 *      url="http://localhost",
 *      description="Localhost"
 * )
 *
 * @OA\SecurityScheme(
 *     type="http",
 *     scheme="bearer",
 *     securityScheme="bearerAuth",
 *     description="API Key Authentication",
 *     name="Authorization",
 *     in="header",
 * )
 */
class Controller extends BaseController
{

    use AuthorizesRequests, ValidatesRequests;
}
