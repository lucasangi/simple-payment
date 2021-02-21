<?php

declare(strict_types=1);

namespace SimplePayment\Tests\Framework\Exception\Infrastructure\Handler;

use Error;
use Exception;
use PHPUnit\Framework\TestCase;
use SimplePayment\Framework\Exception\Domain\ErrorHandler;
use SimplePayment\Framework\Exception\Infrastructure\Handler\DebugErrorHandler;
use Throwable;

use function get_class;
use function json_encode;

class DebugErrorHandlerTest extends TestCase
{
    private ErrorHandler $handler;

    protected function setUp(): void
    {
        $this->handler = new DebugErrorHandler();
    }

    public function testCanHandleShouldReturnTrueForBaseExceptionClass(): void
    {
        $canDeal = $this->handler->canHandle(new Exception());

        $this->assertEquals(true, $canDeal);
    }

    public function testCanHandleShouldReturnTrueForBaseErrorClass(): void
    {
        $canDeal = $this->handler->canHandle(new Error());
        $this->assertEquals(true, $canDeal);
    }

    /** @dataProvider  baseExceptionProvider  */
    public function testShouldHandleTheBaseException(Throwable $exception): void
    {
        $response = $this->handler->handle($exception);

        $exceptionAsArray = $this->throwableDataArrayTransformer($exception);
        if ($exception->getPrevious() instanceof Throwable) {
            $exceptionAsArray['previous'] = $this->throwableDataArrayTransformer($exception->getPrevious());
        }

        $this->assertJsonStringEqualsJsonString(
            json_encode($exceptionAsArray) ?: '',
            $response->getContent() ?: ''
        );

        $this->assertEquals(500, $response->getStatusCode());
    }

    /** @dataProvider baseErrorProvider */
    public function testShouldHandleTheBaseError(Error $error): void
    {
        $response = $this->handler->handle($error);

        $errorAsArray = $this->throwableDataArrayTransformer($error);
        if ($error->getPrevious() instanceof Throwable) {
            $errorAsArray['previous'] = $this->throwableDataArrayTransformer($error->getPrevious());
        }

        $this->assertJsonStringEqualsJsonString(
            json_encode($errorAsArray) ?: '',
            $response->getContent() ?: ''
        );

        $this->assertEquals(500, $response->getStatusCode());
    }

    /** @return array<string, int|string|null> */
    private function throwableDataArrayTransformer(Throwable $exception): array
    {
        return [
            'class' => get_class($exception),
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'previous' => null,
        ];
    }

    /** @return array<mixed> */
    public function baseErrorProvider(): array
    {
        return [
            'Base Error' => [new Error('Base Error')],
            'Base Error With Previous' => [new Error('Base Error', 0, new Error('Previous'))],
        ];
    }

    /** @return array<mixed> */
    public function baseExceptionProvider(): array
    {
        return [
            'Base Exception' => [new Exception('Base Exeception')],
            'Base Exception With Previous' => [new Exception('Base Exeception', 0, new Exception('Previous'))],
        ];
    }
}
