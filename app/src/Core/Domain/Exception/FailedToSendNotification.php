<?php

declare(strict_types=1);

namespace SimplePayment\Core\Domain\Exception;

use Lcobucci\ErrorHandling\Problem\ServiceUnavailable;
use Lcobucci\ErrorHandling\Problem\Titled;
use RuntimeException;
use Throwable;

class FailedToSendNotification extends RuntimeException implements ServiceUnavailable, Titled
{
    public static function fromConnectionError(Throwable $previousException): self
    {
        return new self(
            'An error occurred on send notification.',
            0,
            $previousException
        );
    }

    public function getTitle(): string
    {
        return 'Notification sending failed.';
    }
}
