<?php

declare(strict_types=1);

namespace SimplePayment\Core\Infrastructure\Middleware;

use SimplePayment\Core\Domain\Authenticator;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;

class AuthenticationMiddleware implements MiddlewareInterface
{
    private Authenticator $authenticator;

    public function __construct(Authenticator $authenticator)
    {
        $this->authenticator = $authenticator;
    }

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $this->authenticator->authenticate();

        return $envelope;
    }
}
