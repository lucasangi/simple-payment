<?php

declare(strict_types=1);

namespace SimplePayment\Tests\Framework\Exception\Domain;

use Exception;
use SimplePayment\Framework\Exception\Domain\ErrorHandler;
use Throwable;

class FakeErrorHandler extends ErrorHandler
{
    public function canHandle(Throwable $exception): bool
    {
        return $exception instanceof Exception;
    }
}
