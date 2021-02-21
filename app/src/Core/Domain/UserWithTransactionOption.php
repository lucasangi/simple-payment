<?php

declare(strict_types=1);

namespace SimplePayment\Core\Domain;

abstract class UserWithTransactionOption extends User
{
    public function transferAmountToUser(float $amount, User $user): void
    {
        $this->wallet->withdraw($amount);
        $user->receive($amount);
    }
}
