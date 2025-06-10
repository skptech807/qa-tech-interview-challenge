<?php

declare(strict_types=1);

namespace App\Entity;

use DateTimeInterface;

class Token
{
    readonly public int $id;

    public function __construct(
        public string $hash,
        public string $userId,
        public DateTimeInterface $expireAt
    ) {
    }
}
