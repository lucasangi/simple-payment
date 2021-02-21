<?php

declare(strict_types=1);

namespace SimplePayment\Tests\Framework\Exception\Domain;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use SimplePayment\Framework\Exception\Infrastructure\Handler\DebugErrorHandler;
use SimplePayment\Framework\Exception\Infrastructure\Handler\GenericErrorHandler;
use SimplePayment\Framework\Exception\Infrastructure\Handler\NotFoundHttpErrorHandler;

class ErrorListenerTest extends TestCase
{
    /** @var string[] $errorHandlers */
    private array $errorHandlers = [
        NotFoundHttpErrorHandler::class,
        DebugErrorHandler::class,
        GenericErrorHandler::class,
    ];

    public function testShouldThrowExceptionWhenGivenErrorHandlerListIsEmpty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $listener = new FakeErrorListener([]);
    }

    public function testConstructErrorHandlerChain(): void
    {
        $listener = new FakeErrorListener($this->errorHandlers);

        $errorHandlerChain = $listener->constructErrorHandlerChain();

        $expectedErrorHandlerChain = new NotFoundHttpErrorHandler(
            new DebugErrorHandler(
                new GenericErrorHandler()
            )
        );

        $this->assertEquals($expectedErrorHandlerChain, $errorHandlerChain);
    }
}
