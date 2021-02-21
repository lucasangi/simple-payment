<?php

declare(strict_types=1);

namespace SimplePayment\Framework\Exception\Infrastructure\Handler;

use SimplePayment\Framework\Exception\Domain\ErrorHandler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\ValidationFailedException;
use Throwable;

use function str_replace;

class ValidationFailedErrorHandler extends ErrorHandler
{
    public function canHandle(Throwable $exception): bool
    {
        return $exception instanceof ValidationFailedException;
    }

    public function handle(Throwable $exception): JsonResponse
    {
        if (! $this->canHandle($exception)) {
            return parent::handle($exception);
        }

        $violations = $exception instanceof ValidationFailedException
            ? $exception->getViolations()
            : [];

        $responseData = ['detail' => 'Validation Failed'];
        foreach ($violations as $violation) {
            $violationData = [
                'field' => $violation->getPropertyPath(),
                'message' => $violation->getMessage(),
            ];

            $violationData += $this->formatViolationsParameters($violation->getParameters());

            $responseData['violations'][] = $violationData;
        }

        return new JsonResponse($responseData, Response::HTTP_BAD_REQUEST);
    }

    /**
     * @param string[] $parmeters
     *
     * @return array<string, array<string, string>>
     */
    private function formatViolationsParameters(array $parmeters): array
    {
        $formattedParameters = [];
        foreach ($parmeters as $parameterKey => $parameterValue) {
            $parameterKeyWithoutBraces = $this->removeViolationParameterBraces($parameterKey);
            $formattedParameters['parameters'][$parameterKeyWithoutBraces] = $parameterValue;
        }

        return $formattedParameters;
    }

    private function removeViolationParameterBraces(string $parameterKey): string
    {
        return str_replace(['{{ ', ' }}'], '', $parameterKey);
    }
}
