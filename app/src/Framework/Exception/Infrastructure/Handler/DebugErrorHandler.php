<?php

declare(strict_types=1);

namespace SimplePayment\Framework\Exception\Infrastructure\Handler;

use SimplePayment\Framework\Exception\Domain\ErrorHandler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

use function get_class;

class DebugErrorHandler extends ErrorHandler
{
    public function canHandle(Throwable $exception): bool
    {
        return $exception instanceof Throwable;
    }

    public function handle(Throwable $exception): JsonResponse
    {
        $exceptionAsArray = $this->genereateExceptionArray($exception);

        if ($exception->getPrevious() instanceof Throwable) {
            $exceptionAsArray['previous'] = $this->genereateExceptionArray($exception->getPrevious());
        }

        return new JsonResponse(
            $exceptionAsArray,
            Response::HTTP_INTERNAL_SERVER_ERROR,
        );
    }

    /** @return array<string, int|string|null> */
    private function genereateExceptionArray(Throwable $exception): array
    {
        return [
            'class' => get_class($exception),
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'previous' => null,
        ];
    }
}
