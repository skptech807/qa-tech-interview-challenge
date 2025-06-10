<?php

declare(strict_types=1);

namespace App\Listener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Validator\Exception\ValidationFailedException;

#[AsEventListener('kernel.exception')]
class ValidatorErrorListener
{
    public function onKernelException(ExceptionEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $contentType = $event->getRequest()->getContentTypeFormat();
        if ($contentType !== 'json') {
            return;
        }

        $throwable = $event->getThrowable();
        if (!$throwable->getPrevious() instanceof ValidationFailedException) {
            return;
        }

        $constraintViolation = $throwable->getPrevious()->getViolations()->get(0);
        $message = sprintf(
            '%s: %s',
            $constraintViolation->getPropertyPath(),
            $constraintViolation->getMessage()
        );

        $event->setResponse(
            new JsonResponse(
                ['error' => $message],
                Response::HTTP_BAD_REQUEST
            )
        );
    }
}
