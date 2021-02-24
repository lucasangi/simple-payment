<?php

declare(strict_types=1);

namespace SimplePayment\Core\Domain\Exception;

use Lcobucci\ErrorHandling\Problem\AuthorizationRequired;
use Lcobucci\ErrorHandling\Problem\Titled;
use RuntimeException;
use Throwable;

class AuthenticationFailed extends RuntimeException implements AuthorizationRequired, Titled
{
    public static function fromAuthenticationError(Throwable $previousException): self
    {
        return new self(
            'The authentication request failed.',
            0,
            $previousException
        );
    }

    public function getTitle(): string
    {
        return 'Authentication Failed.';
    }
}
