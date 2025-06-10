<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Account;
use App\Entity\Transaction;
use DateTimeImmutable;
use Tests\Common\PropertyAccessor;

class TransactionFactory
{
    public function create(
        Account $fromAccount,
        Account $toAccount,
        int $amount,
        string $currency,
        string $description
    ): Transaction {
        $transaction = new Transaction(
            $fromAccount,
            $toAccount,
            $amount,
            $currency,
            $description
        );

        PropertyAccessor::setPropertyValue($transaction, 'createdAt', new DateTimeImmutable());

        return $transaction;
    }
}
