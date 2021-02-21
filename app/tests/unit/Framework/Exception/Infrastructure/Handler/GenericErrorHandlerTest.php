<?php

declare(strict_types=1);

namespace SimplePayment\Tests\Framework\Exception\Infrastructure\Handler;

use Error;
use Exception;
use PHPUnit\Framework\TestCase;
use SimplePayment\Framework\Exception\Domain\ErrorHandler;
use SimplePayment\Framework\Exception\Infrastructure\Handler\GenericErrorHandler;

use function json_encode;

class GenericErrorHandlerTest extends TestCase
{
    private ErrorHandler $handler;

    protected function setUp(): void
    {
        $this->handler = new GenericErrorHandler();
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

    public function testShouldHandleTheBaseException(): void
    {
        $response = $this->handler->handle(new Exception());

        $this->assertJsonStringEqualsJsonString(
            json_encode(['detail' => 'Internal Server Error']) ?: '',
            $response->getContent() ?: ''
        );
        $this->assertEquals(500, $response->getStatusCode());
    }

    public function testShouldHandleTheBaseError(): void
    {
        $response = $this->handler->handle(new Error());

        $this->assertJsonStringEqualsJsonString(
            json_encode(['detail' => 'Internal Server Error']) ?: '',
            $response->getContent() ?: ''
        );
        $this->assertEquals(500, $response->getStatusCode());
    }
}
