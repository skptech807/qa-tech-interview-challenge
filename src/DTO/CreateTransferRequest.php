<?php

declare(strict_types=1);

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class CreateTransferRequest
{
    public function __construct(
        #[Assert\NotBlank]
        public string $fromAccount,

        #[Assert\NotBlank]
        public string $toAccount,

        #[Assert\NotBlank]
        #[Assert\Regex(pattern: '/^\d+$/')]
        #[Assert\GreaterThan(0)]
        public string $amount,

        #[Assert\NotBlank]
        #[Assert\Type('string')]
        #[Assert\Choice(choices: ['EUR', 'USD'])]
        public string $currency,

        #[Assert\NotBlank]
        public string $description
    ) {
    }

}
