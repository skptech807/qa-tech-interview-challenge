<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Token;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

readonly class Authenticator
{
    public const TOKEN_LIFETIME = 3600;

    public function __construct(
        private TokenGenerator $tokenGenerator,
        private EntityManagerInterface $entityManager,
        private UserProviderInterface $userProvider
    ) {
    }

    public function authenticate(string $login, string $password): ?Token
    {
        try {
            $user = $this->userProvider->loadUserByIdentifier($login);
            if (!$user instanceof PasswordAuthenticatedUserInterface) {
                return null;
            }

            if ($user->getPassword() !== $password) {
                return null;
            }
            $token = new Token(
                $this->tokenGenerator->generate(),
                $login,
                new DateTimeImmutable(sprintf('+%s seconds', self::TOKEN_LIFETIME))
            );
            $this->entityManager->persist($token);
            $this->entityManager->flush();

            return $token;
        } catch (UserNotFoundException) {
            return null;
        }
    }
}
