<?php

declare(strict_types=1);

namespace SimplePayment\Tests\Framework\Exception\Infrastructure\Handler\Exception;

use Lcobucci\ErrorHandling\Problem\ResourceNotFound;
use RuntimeException;

final class UserNotFound extends RuntimeException implements ResourceNotFound
{
}
