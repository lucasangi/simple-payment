<?php

declare(strict_types=1);

namespace SimplePayment\Core\Domain\Exception;

use InvalidArgumentException;
use Lcobucci\ErrorHandling\Problem\Detailed;
use Lcobucci\ErrorHandling\Problem\ResourceNotFound;
use Lcobucci\ErrorHandling\Problem\Titled;

class UserNotFound extends InvalidArgumentException implements ResourceNotFound, Titled, Detailed
{
    private int $id;

    public static function withGivenId(int $id): self
    {
        $exception = new self('No user was found with the given id.');
        $exception->id = $id;

        return $exception;
    }

    public function getTitle(): string
    {
        return 'User not found.';
    }

    /** @return array<string, int> */
    public function getExtraDetails(): array
    {
        return [
            'id' => $this->id,
        ];
    }
}
