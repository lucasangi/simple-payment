<?php

declare(strict_types=1);

namespace SimplePayment\Tests\Core\Application\Exception;

use PHPUnit\Framework\TestCase;
use SimplePayment\Core\Application\Exception\InvalidTransaction;

class InvalidTransactionTest extends TestCase
{
    public function testShouldThrowExceptionWhenTryTransferMoneyWithShopkeeperAsPayer(): void
    {
        $exception = InvalidTransaction::fromShopkeeperAsPayer();

        $this->assertEquals('Shopkeeper user can not make money transactions.', $exception->getMessage());
        $this->assertEquals('Invalid Transaction.', $exception->getTitle());
    }
}
