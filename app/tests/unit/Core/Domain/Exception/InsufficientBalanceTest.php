<?php

declare(strict_types=1);

namespace SimplePayment\Tests\Core\Domain\Exception;

use PHPUnit\Framework\TestCase;
use SimplePayment\Core\Domain\Exception\InsufficientBalance;

class InsufficientBalanceTest extends TestCase
{
    public function testShouldThrowExceptionWhenTryWithdrawAmountWithInsufficientBallance(): void
    {
        $exception = InsufficientBalance::forWithdraw();

        $this->assertEquals('The payer do not have enough balance for withdraw.', $exception->getMessage());
        $this->assertEquals('Insufficient Wallet Balance.', $exception->getTitle());
    }
}
