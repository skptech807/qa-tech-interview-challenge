<?php

declare(strict_types=1);

namespace App\Entity;

class Account
{
    public function __construct(
        public string $id,
        public int $balance,
        public string $currency
    ) {
    }
}
