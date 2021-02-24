<?php

declare(strict_types=1);

namespace SimplePayment\Core\Domain\Exception;

use InvalidArgumentException;
use Lcobucci\ErrorHandling\Problem\Detailed;
use Lcobucci\ErrorHandling\Problem\ResourceNotFound;
use Lcobucci\ErrorHandling\Problem\Titled;
use SimplePayment\Framework\Id\Domain\Id;

class UserNotFound extends InvalidArgumentException implements ResourceNotFound, Titled, Detailed
{
    private Id $id;

    public static function withGivenId(Id $id): self
    {
        $exception = new self('No user was found with the given id.');
        $exception->id = $id;

        return $exception;
    }

    public function getTitle(): string
    {
        return 'User not found.';
    }

    /** @return array<string, string> */
    public function getExtraDetails(): array
    {
        return [
            'id' => $this->id->toString(),
        ];
    }
}
