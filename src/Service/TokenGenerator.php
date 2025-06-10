<?php

declare(strict_types=1);

namespace App\Service;

readonly class TokenGenerator
{
    public function generate(): string
    {
        return md5(microtime() . rand(0, 1000000));
    }
}
