<?php

declare(strict_types=1);

namespace SimplePayment\Core\Domain\Exception;

use Lcobucci\ErrorHandling\Problem\InvalidRequest;
use Lcobucci\ErrorHandling\Problem\Titled;
use RuntimeException;

class InsufficientBalance extends RuntimeException implements InvalidRequest, Titled
{
    public static function forWithdraw(): self
    {
        return new self('The payer do not have enough balance for withdraw.');
    }

    public function getTitle(): string
    {
        return 'Insufficient Wallet Balance.';
    }
}
