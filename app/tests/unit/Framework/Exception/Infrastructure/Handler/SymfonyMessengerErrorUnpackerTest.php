<?php

declare(strict_types=1);

namespace SimplePayment\Tests\Framework\Exception\Infrastructure\Handler;

use Exception;
use PHPUnit\Framework\TestCase;
use SimplePayment\Core\Domain\Exception\InsufficientBalance;
use SimplePayment\Framework\Exception\Infrastructure\Handler\GenericErrorHandler;
use SimplePayment\Framework\Exception\Infrastructure\Handler\SymfonyMessengerErrorUnpacker;
use stdClass;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\HandlerFailedException;

class SymfonyMessengerErrorUnpackerTest extends TestCase
{
    public function testShouldIndicateCanHandleException(): void
    {
        $handler = new SymfonyMessengerErrorUnpacker(new GenericErrorHandler());

        $envelope = $this->aEnvelope();
        $exception = new HandlerFailedException(
            $envelope,
            [InsufficientBalance::forWithdraw()]
        );

        $canDeal = $handler->canHandle($exception);

        $this->assertEquals(true, $canDeal);
    }

    public function testShouldIndicateCannotHandleException(): void
    {
        $exception = new Exception();

        $handler = new SymfonyMessengerErrorUnpacker(new GenericErrorHandler());
        $canDeal = $handler->canHandle($exception);

        $this->assertEquals(false, $canDeal);
    }

    public function testShouldHandleUnpackedExceptionWithNextHandler(): void
    {
        $nextHandler = new GenericErrorHandler();

        $handler = new SymfonyMessengerErrorUnpacker($nextHandler);

        $nestedException = InsufficientBalance::forWithdraw();
        $envelope = $this->aEnvelope();

        $exception = new HandlerFailedException(
            $envelope,
            [$nestedException]
        );

        $response = $handler->handle($exception);

        $expectedResponse = $nextHandler->handle($nestedException);

        $this->assertEquals($expectedResponse, $response);
    }

    private function aEnvelope(): Envelope
    {
        return new Envelope(new stdClass(), []);
    }
}
