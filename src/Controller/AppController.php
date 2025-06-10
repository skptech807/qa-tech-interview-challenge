<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;

abstract class AppController
{
    protected function createErrorResponse(string $errorText, int $statusCode): JsonResponse
    {
        return new JsonResponse(['error' => $errorText], $statusCode);
    }
}
