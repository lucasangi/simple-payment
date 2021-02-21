<?php

declare(strict_types=1);

namespace SimplePayment\Framework\Exception\Infrastructure\Handler;

use Lcobucci\ErrorHandling\Problem\AuthorizationRequired;
use Lcobucci\ErrorHandling\Problem\Conflict;
use Lcobucci\ErrorHandling\Problem\Detailed;
use Lcobucci\ErrorHandling\Problem\Forbidden;
use Lcobucci\ErrorHandling\Problem\InvalidRequest;
use Lcobucci\ErrorHandling\Problem\ResourceNoLongerAvailable;
use Lcobucci\ErrorHandling\Problem\ResourceNotFound;
use Lcobucci\ErrorHandling\Problem\ServiceUnavailable;
use Lcobucci\ErrorHandling\Problem\Titled;
use Lcobucci\ErrorHandling\Problem\Typed;
use Lcobucci\ErrorHandling\Problem\UnprocessableRequest;
use Lcobucci\ErrorHandling\StatusCodeExtractionStrategy\ClassMap;
use SimplePayment\Framework\Exception\Domain\ErrorHandler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Throwable;

class LcobucciErrorHandler extends ErrorHandler
{
    public function canHandle(Throwable $exception): bool
    {
        switch (true) {
            case $exception instanceof InvalidRequest:
            case $exception instanceof AuthorizationRequired:
            case $exception instanceof Forbidden:
            case $exception instanceof ResourceNotFound:
            case $exception instanceof Conflict:
            case $exception instanceof ResourceNoLongerAvailable:
            case $exception instanceof UnprocessableRequest:
            case $exception instanceof ServiceUnavailable:
                return true;

            default:
                return false;
        }
    }

    public function handle(Throwable $exception): JsonResponse
    {
        if (! $this->canHandle($exception)) {
            return parent::handle($exception);
        }

        $data = [
            'type' => $exception instanceof Typed ? $exception->getTypeUri() : null,
            'title' => $exception instanceof Titled ? $exception->getTitle() : null,
            'detail' => $exception->getMessage(),
        ];

        if ($exception instanceof Detailed) {
            $data += $exception->getExtraDetails();
        }

        return new JsonResponse(
            $data,
            (new ClassMap())->extractStatusCode($exception),
        );
    }
}
