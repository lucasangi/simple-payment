<?php

declare(strict_types=1);

namespace SimplePayment\Framework\Exception\Infrastructure\Handler;

use SimplePayment\Framework\Exception\Domain\ErrorHandler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Throwable;

class SymfonyMessengerErroUnpacker extends ErrorHandler
{
    public function canHandle(Throwable $exception): bool
    {
        return $exception instanceof HandlerFailedException;
    }

    public function handle(Throwable $exception): JsonResponse
    {
        $nestedException = $exception instanceof HandlerFailedException
            ? $exception->getNestedExceptions()[0]
            : $exception;

        return parent::handle($nestedException);
    }
}
