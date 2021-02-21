<?php

declare(strict_types=1);

namespace SimplePayment\Framework\Exception\Infrastructure\Handler;

use SimplePayment\Framework\Exception\Domain\ErrorHandler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class NotFoundHttpErrorHandler extends ErrorHandler
{
    public function canHandle(Throwable $exception): bool
    {
        return $exception instanceof NotFoundHttpException;
    }

    public function handle(Throwable $exception): JsonResponse
    {
        if (! $this->canHandle($exception)) {
            return parent::handle($exception);
        }

        return new JsonResponse([
            'detail' => $exception->getMessage(),
        ], Response::HTTP_NOT_FOUND);
    }
}
