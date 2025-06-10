<?php

declare(strict_types=1);

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Account;

readonly class DBRefresher
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function refresh(): void
    {
        $connection = $this->entityManager->getConnection();

        $tablesToTruncate = [
            'tokens',
            'transactions',
            'accounts'
        ];

        foreach ($tablesToTruncate as $table) {
            $connection->executeStatement("TRUNCATE TABLE {$table} CASCADE");
        }

        $accountsData = [
            ['LT123456789', 1000 * 100, 'USD'],
            ['LT987654321', 500 * 100, 'USD'],
            ['LT555555555', 10 * 100, 'USD']
        ];

        foreach ($accountsData as [$accountNumber, $amount, $currency]) {
            $account = new Account($accountNumber, $amount, $currency);
            $this->entityManager->persist($account);
        }

        $this->entityManager->flush();
    }
}
