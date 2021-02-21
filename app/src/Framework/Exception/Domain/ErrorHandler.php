<?php

declare(strict_types=1);

namespace SimplePayment\Framework\Exception\Domain;

use InvalidArgumentException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Throwable;

abstract class ErrorHandler
{
    private ?ErrorHandler $next;

    public function __construct(?ErrorHandler $next = null)
    {
        $this->next = $next;
    }

    abstract public function canHandle(Throwable $exception): bool;

    public function handle(Throwable $exception): JsonResponse
    {
        if ($this->next instanceof ErrorHandler) {
            return $this->next->handle($exception);
        }

        throw new InvalidArgumentException('This error cannot be handled.');
    }
}
