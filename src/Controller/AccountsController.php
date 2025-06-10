<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Transaction;
use App\Enum\TransactionType;
use App\Repository\AccountRepository;
use App\Repository\TransactionRepository;
use DateTimeImmutable;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

class AccountsController extends AppController
{
    public function __construct(
        private readonly AccountRepository $repository,
        private readonly TransactionRepository $transactionRepository
    ) {
    }

    #[Route('/accounts/{accountId}', methods: ['GET'])]
    public function account(string $accountId): JsonResponse
    {
        $account = $this->repository->find($accountId);

        if ($account === null) {
            return $this->createErrorResponse(
                'Account not found',
                Response::HTTP_NOT_FOUND
            );
        }

        return new JsonResponse([
            'accountId' => $account->id,
            'balance' => $account->balance,
            'currency' => $account->currency
        ]);
    }

    #[Route('/accounts/{accountId}/transactions', methods: ['GET'])]
    public function transactions(
        string $accountId,
        #[MapQueryParameter('from')] ?string $fromValue = null,
        #[MapQueryParameter('to')] ?string $toValue = null,
        #[MapQueryParameter] int $limit = 100,
    ): JsonResponse {
        try {
            $from = $fromValue ? new DateTimeImmutable($fromValue) : null;
        } catch (Exception) {
            $from = null;
        }

        try {
            $to = $toValue ? new DateTimeImmutable($toValue) : null;
        } catch (Exception) {
            $to = null;
        }

        $account = $this->repository->find($accountId);
        if ($account === null) {
            return $this->createErrorResponse(
                'Account not found',
                Response::HTTP_NOT_FOUND
            );
        }

        $transactions = $this->transactionRepository->findTransactionsBetweenDates(
            $accountId,
            $from,
            $to,
            $limit
        );

        $result = array_map(fn(Transaction $transaction) => [
            'id' => $transaction->id,
            'type' => $transaction->toAccount->id === $accountId
                ? TransactionType::INCOMING->value
                : TransactionType::OUTGOING->value,
            'amount' => $transaction->amount,
            'currency' => $transaction->currency,
            'otherParty' => $transaction->toAccount->id === $accountId
                ? $transaction->fromAccount->id
                : $transaction->toAccount->id,
            'description' => $transaction->description,
            'timestamp' => $transaction->createdAt->format(DATE_RFC3339)
        ], $transactions);

        return new JsonResponse($result);
    }}
