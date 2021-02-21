<?php

declare(strict_types=1);

namespace SimplePayment\Tests\Framework\Exception\Infrastructure\Handler;

use Exception;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use SimplePayment\Framework\Exception\Domain\ErrorHandler;
use SimplePayment\Framework\Exception\Infrastructure\Handler\GenericErrorHandler;
use SimplePayment\Framework\Exception\Infrastructure\Handler\NotFoundHttpErrorHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use function json_encode;

class NotFoundHttpErrorHandlerTest extends TestCase
{
    private ErrorHandler $handler;

    protected function setUp(): void
    {
        $this->handler = new NotFoundHttpErrorHandler();
    }

    public function testShouldIndicateCanHandleException(): void
    {
        $exception = new NotFoundHttpException('No route found for \"GET /example');
        $canDeal = $this->handler->canHandle($exception);

        $this->assertEquals(true, $canDeal);
    }

    public function testShouldIndicateCannotHandleException(): void
    {
        $exception = new Exception();
        $canDeal = $this->handler->canHandle($exception);

        $this->assertEquals(false, $canDeal);
    }

    public function testShouldHandleException(): void
    {
        $exception = new NotFoundHttpException('No route found for \"GET /example');

        $response = $this->handler->handle($exception);
        $expectedResponse = ['detail' => 'No route found for \"GET /example'];

        $this->assertJsonStringEqualsJsonString(
            json_encode($expectedResponse) ?: '',
            $response->getContent() ?: ''
        );
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testShouldHandleUnsupportedExceptionWithNextHandler(): void
    {
        $unsupportedException = new RuntimeException();

        $next = new GenericErrorHandler();
        $handler = new NotFoundHttpErrorHandler($next);

        $response = $handler->handle($unsupportedException);

        $this->assertJsonStringEqualsJsonString(
            json_encode(['detail' => 'Internal Server Error']) ?: '',
            $response->getContent() ?: ''
        );
        $this->assertEquals(500, $response->getStatusCode());
    }

    public function testShouldThrowExceptionOnTryHandleUnsupportedException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('This error cannot be handled.');

        $unsupportedException = new RuntimeException();
        $this->handler->handle($unsupportedException);
    }
}
