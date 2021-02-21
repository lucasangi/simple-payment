<?php

declare(strict_types=1);

namespace SimplePayment\Framework\Exception\Infrastructure\Handler;

use SimplePayment\Framework\Exception\Domain\ErrorHandler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class GenericErrorHandler extends ErrorHandler
{
    public function canHandle(Throwable $exception): bool
    {
        return $exception instanceof Throwable;
    }

    public function handle(Throwable $exception): JsonResponse
    {
        return new JsonResponse(
            ['detail' => 'Internal Server Error'],
            Response::HTTP_INTERNAL_SERVER_ERROR,
        );
    }
}
