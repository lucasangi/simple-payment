<?php

declare(strict_types=1);

namespace SimplePayment\Tests\Core\Domain;

use PHPUnit\Framework\TestCase;
use SimplePayment\Core\Domain\Exception\InsufficientBalance;
use SimplePayment\Core\Domain\Wallet;

class WalletTest extends TestCase
{
    public function testShouldCreateEmptyWalletAndDepositAnAmountIntoIt(): void
    {
        $wallet = Wallet::create(0);
        $this->assertEquals(0, $wallet->amount());

        $wallet->deposit(100);
        $this->assertEquals(100, $wallet->amount());
    }

    public function testShouldCreateWalletAndWithdrawAmountFromIt(): void
    {
        $wallet = Wallet::create(100);
        $this->assertEquals(100, $wallet->amount());

        $wallet->withdraw(33);
        $this->assertEquals(67, $wallet->amount());
    }

    public function testShouldThrowExceptionWhenWalletHasInsufficientBalanceForWithdraw(): void
    {
        $this->expectException(InsufficientBalance::class);
        $wallet = Wallet::create(100);

        $wallet->withdraw(1000);
    }
}
