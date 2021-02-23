<?php

declare(strict_types=1);

namespace SimplePayment\Framework\Id\Domain\Exception;

use Lcobucci\ErrorHandling\Problem\InvalidRequest;
use Lcobucci\ErrorHandling\Problem\Titled;
use RuntimeException;

use function sprintf;

class InvalidId extends RuntimeException implements InvalidRequest, Titled
{
    public static function invalidIdString(string $id): self
    {
        return new self(
            sprintf('Invalid UUID string: %s.', $id)
        );
    }

    public function getTitle(): string
    {
        return 'Invalid id.';
    }
}
