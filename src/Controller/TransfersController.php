<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\CreateTransferRequest;
use App\Exception\InsufficientFundsException;
use App\Exception\TransferLogicException;
use App\Repository\AccountRepository;
use App\Service\TransferMaker;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Throwable;

class TransfersController extends AppController
{
    public function __construct(
        private readonly TransferMaker $transferMaker,
        private readonly AccountRepository $accountRepository,
    ) {
    }

    #[Route('/transfers', methods: ['POST'])]
    public function transfer(
        #[MapRequestPayload(validationFailedStatusCode: Response::HTTP_BAD_REQUEST)]
        CreateTransferRequest $dto
    ): JsonResponse {
        $fromAccount = $this->accountRepository->find($dto->fromAccount);
        if ($fromAccount === null) {
            return $this->createErrorResponse('From Account not found', Response::HTTP_NOT_FOUND);
        }

        $toAccount = $this->accountRepository->find($dto->toAccount);
        if ($toAccount === null) {
            return $this->createErrorResponse('To Account not found', Response::HTTP_NOT_FOUND);
        }

        try {
            $transaction = $this->transferMaker->doMake(
                $fromAccount,
                $toAccount,
                (int)$dto->amount,
                $dto->currency,
                $dto->description
            );

            return new JsonResponse([
                'transferId' => $transaction->id,
                'status' => 'done',
                'timestamp' => $transaction->createdAt->format(DATE_RFC3339)
            ]);
        } catch (TransferLogicException $exception) {
            return $this->createErrorResponse($exception->getMessage(), Response::HTTP_BAD_REQUEST);
        } catch (InsufficientFundsException $exception) {
            return $this->createErrorResponse($exception->getMessage(), Response::HTTP_PAYMENT_REQUIRED);
        } catch (Throwable) {
            return $this->createErrorResponse('Internal Error', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
