<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\LoginRequest;
use App\Service\Authenticator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

class AuthController extends AppController
{
    public function __construct(
        private readonly Authenticator $authenticator,
    ) {
    }

    #[Route('/auth/login', methods: ['POST'])]
    public function login(
        #[MapRequestPayload(validationFailedStatusCode: Response::HTTP_BAD_REQUEST)] LoginRequest $dto
    ): JsonResponse {
        $token = $this->authenticator->authenticate(
            $dto->username,
            $dto->password
        );

        if ($token !== null) {
            return new JsonResponse([
                'token' => $token->hash,
                'expiresIn' => Authenticator::TOKEN_LIFETIME
            ]);
        }

        return $this->createErrorResponse('Invalid credentials', Response::HTTP_UNAUTHORIZED);
    }
}
