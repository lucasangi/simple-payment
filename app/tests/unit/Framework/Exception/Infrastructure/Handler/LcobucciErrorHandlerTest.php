<?php

declare(strict_types=1);

namespace SimplePayment\Framework\Tests\Exception\Infrastructure\Handler;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use SimplePayment\Framework\Exception\Domain\ErrorHandler;
use SimplePayment\Framework\Exception\Infrastructure\Handler\GenericErrorHandler;
use SimplePayment\Framework\Exception\Infrastructure\Handler\LcobucciErrorHandler;
use SimplePayment\Tests\Framework\Exception\Infrastructure\Handler\Exception\InsufficientBalance;
use SimplePayment\Tests\Framework\Exception\Infrastructure\Handler\Exception\Unauthorized;
use SimplePayment\Tests\Framework\Exception\Infrastructure\Handler\Exception\UserNotFound;
use Throwable;

use function json_encode;

class LcobucciErrorHandlerTest extends TestCase
{
    private ErrorHandler $handler;

    protected function setUp(): void
    {
        $this->handler = new LcobucciErrorHandler();
    }

    /** @dataProvider supportedExceptionProvider */
    public function testShouldIndicateCanHandleException(Throwable $exception): void
    {
        $canDeal = $this->handler->canHandle($exception);

        $this->assertEquals(true, $canDeal);
    }

    public function testShouldIndicateCannotHandleException(): void
    {
        $exception = new InvalidArgumentException();
        $canDeal = $this->handler->canHandle($exception);

        $this->assertEquals(false, $canDeal);
    }

    /**
     * @param string[] $expectedResponse
     *
     * @dataProvider supportedExceptionProvider
     */
    public function testShouldHandleException(
        Throwable $exception,
        array $expectedResponse,
        int $expectedStatusCode
    ): void {
        $response = $this->handler->handle($exception);

        $this->assertJsonStringEqualsJsonString(
            json_encode($expectedResponse) ?: '',
            $response->getContent() ?: ''
        );
        $this->assertEquals($expectedStatusCode, $response->getStatusCode());
    }

    public function testShouldHandleUnsupportedExceptionWithNextHandler(): void
    {
        $unsupportedException = new InvalidArgumentException();

        $next = new GenericErrorHandler();
        $handler = new LcobucciErrorHandler($next);

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

        $unsupportedException = new InvalidArgumentException();
        $this->handler->handle($unsupportedException);
    }

    /** @return array<mixed> */
    public function supportedExceptionProvider(): array
    {
        return [
            'when there is just a detail' => [
                new UserNotFound('User not found'),
                ['detail' => 'User not found', 'type' => null, 'title' => null],
                404,
            ],
            'when there is just a detail and title' => [
                new Unauthorized('Invalid credencial'),
                [
                    'detail' => 'Invalid credencial',
                    'title' => 'The credencial information is not valid.',
                    'type' => null,
                ],
                401,
            ],
            'when there is complete error information' => [
                InsufficientBalance::forPurchase(30, 50),
                [
                    'detail' => 'Your current balance is 30, but that costs 50.',
                    'type' => 'https://example.com/probs/insuficient-balance',
                    'title' => 'You do not have enough balance.',
                    'balance' => 30,
                ],
                403,
            ],
        ];
    }
}
