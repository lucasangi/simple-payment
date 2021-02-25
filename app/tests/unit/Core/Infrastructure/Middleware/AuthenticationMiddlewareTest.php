<?php

declare(strict_types=1);

namespace SimplePayment\Tests\Core\Infrastructure\Middleware;

use PHPUnit\Framework\TestCase;
use SimplePayment\Core\Domain\Authenticator;
use SimplePayment\Core\Infrastructure\Middleware\AuthenticationMiddleware;
use stdClass;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;

use function assert;

class AuthenticationMiddlewareTest extends TestCase
{
    public function testMiddlewareShouldUseAuthenticator(): void
    {
        $mockAuthenticator = $this->getMockBuilder(Authenticator::class)
            ->onlyMethods(['authenticate'])
            ->getMock();

        $mockAuthenticator->expects($this->once())->method('authenticate');

        assert($mockAuthenticator instanceof Authenticator);

        $middleware = new AuthenticationMiddleware($mockAuthenticator);

        $envelope = $this->aEnvelope();
        $mockStack = $this->getMockBuilder(StackInterface::class)->getMock();
        $mockNextMiddleware = $this->getMockBuilder(MiddlewareInterface::class)->getMock();

        $mockStack->expects($this->once())
            ->method('next')
            ->willReturn($mockNextMiddleware);

        $mockNextMiddleware->expects($this->once())
            ->method('handle')
            ->willReturn(new Envelope($envelope));

        assert($mockStack instanceof StackInterface);
        assert($mockNextMiddleware instanceof MiddlewareInterface);

        $middleware->handle($envelope, $mockStack);
    }

    private function aEnvelope(): Envelope
    {
        return new Envelope(new stdClass(), []);
    }
}
