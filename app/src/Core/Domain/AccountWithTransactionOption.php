<?php

declare(strict_types=1);

namespace SimplePayment\Core\Domain;

abstract class AccountWithTransactionOption extends Account
{
    public function transferAmountToAccount(float $amount, Account $user): void
    {
        $this->wallet->withdraw($amount);
        $user->receive($amount);
    }
}
