<?php

declare(strict_types=1);

namespace App\Service;

use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use App\Entity\Account;
use App\Entity\Transaction;
use App\Exception\InsufficientFundsException;
use App\Exception\TransferLogicException;

readonly class TransferMaker
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private TransactionFactory $transactionFactory
    ) {
    }

    /**
     * @throws InsufficientFundsException
     * @throws TransferLogicException
     * @throws Exception
     */
    public function doMake(
        Account $fromAccount,
        Account $toAccount,
        int $amount,
        string $currency,
        string $description
    ): ?Transaction {
        if ($fromAccount->id === $toAccount->id) {
            throw new TransferLogicException('From and To accounts are same');
        }

        if ($fromAccount->currency !== $currency) {
            throw new TransferLogicException('Currency mismatch');
        }

        if ($fromAccount->balance < $amount) {
            throw new InsufficientFundsException('Insufficient funds');
        }

        $transaction = $this->transactionFactory->create(
            $fromAccount,
            $toAccount,
            $amount,
            $currency,
            $description
        );

        $this->entityManager->beginTransaction();
        try {
            $this->entityManager->persist($transaction);
            $fromAccount->balance -= $amount;
            $toAccount->balance += $amount;
            $this->entityManager->flush();
            $this->entityManager->commit();

            return $transaction;
        } catch (Exception $exception) {
            $this->entityManager->rollback();

            throw $exception;
        }
    }
}
