<?php

declare(strict_types=1);

namespace SimplePayment\Tests\Framework\Exception\Infrastructure\Handler\Exception;

use Lcobucci\ErrorHandling\Problem\AuthorizationRequired;
use Lcobucci\ErrorHandling\Problem\Titled;
use RuntimeException;

final class Unauthorized extends RuntimeException implements AuthorizationRequired, Titled
{
    public function getTitle(): string
    {
        return 'The credencial information is not valid.';
    }
}
