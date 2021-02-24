<?php

declare(strict_types=1);

namespace SimplePayment\Core\Application\Exception;

use InvalidArgumentException;
use Lcobucci\ErrorHandling\Problem\Forbidden;
use Lcobucci\ErrorHandling\Problem\Titled;

class InvalidTransaction extends InvalidArgumentException implements Forbidden, Titled
{
    public static function fromShopkeeperAsPayer(): self
    {
        return new self('Shopkeeper user can not make money transactions.');
    }

    public function getTitle(): string
    {
        return 'Invalid Transaction.';
    }
}
