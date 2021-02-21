<?php

declare(strict_types=1);

namespace SimplePayment\Tests\Framework\Exception\Domain;

use Exception;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use SimplePayment\Framework\Exception\Infrastructure\Handler\GenericErrorHandler;
use Symfony\Component\HttpFoundation\Response;

class ErrorHandlerTest extends TestCase
{
    public function testShouldHandleUnsupportedExceptionWithNextHandler(): void
    {
        $exception = new Exception('An Exception');

        $next = new GenericErrorHandler();
        $handler = new FakeErrorHandler($next);
        $response = $handler->handle($exception);

        $this->assertInstanceOf(Response::class, $response);
    }

    public function testShouldThrowExceptionOnTryHandleUnsupportedExceptionWithoutNextHandler(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('This error cannot be handled.');

        $exception = new Exception('An Exception');

        $handler = new FakeErrorHandler();
        $handler->handle($exception);
    }
}
