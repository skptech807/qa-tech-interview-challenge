<?php

declare(strict_types=1);

namespace App\Repository;

use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Transaction;

class TransactionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Transaction::class);
    }

    public function findTransactionsBetweenDates(
        string $accountId,
        DateTimeInterface $startDate = null,
        DateTimeInterface $endDate = null,
        int $limit = 20
    ): array {
        $query = $this->createQueryBuilder('t')
            ->where('(t.fromAccount = :accountId OR t.toAccount = :accountId)')
            ->setParameter('accountId', $accountId)
            ->setMaxResults($limit)
            ->orderBy('t.createdAt', 'ASC');

        if ($startDate !== null) {
            $query->andWhere('t.createdAt >= :startDate');
            $query->setParameter('startDate', $startDate);
        }

        if ($endDate !== null) {
            $query->andWhere('t.createdAt <= :endDate');
            $query->setParameter('endDate', $endDate);
        }

        return $query->getQuery()->getResult();
    }
}
