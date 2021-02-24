<?php

declare(strict_types=1);

namespace SimplePayment\Tests\Core\Infrastructure\Middleware;

use PHPUnit\Framework\TestCase;
use SimplePayment\Core\Domain\Authenticator;
use SimplePayment\Core\Infrastructure\Middleware\AuthenticationMiddleware;
use stdClass;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Component\Messenger\Middleware\TraceableStack;

class AuthenticationMiddlewareTest extends TestCase {
    
    public function testMiddlewareShouldUseAuthenticator(): void {

        $mockAuthenticator = $this->getMockBuilder(Authenticator::class)
            ->onlyMethods(['authenticate'])
            ->getMock();
        
        $mockAuthenticator->expects($this->once())->method('authenticate');

        assert($mockAuthenticator instanceof Authenticator);

        $middleware = new AuthenticationMiddleware($mockAuthenticator);

        $envelope = new Envelope(new stdClass(),[]);
        $mockStack = $this->getMockBuilder(StackInterface::class)->getMock();

        assert($mockStack instanceof StackInterface);

        $middleware->handle($envelope, $mockStack);
    }
}