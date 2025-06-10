<?php

declare(strict_types=1);

namespace App\Entity;

use DateTimeInterface;

class Transaction
{
    readonly public int $id;
    readonly public DateTimeInterface $createdAt;

    public function __construct(
        public Account $fromAccount,
        public Account $toAccount,
        public int $amount,
        public string $currency,
        public string $description
    ) {
    }
}
