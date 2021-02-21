<?php

declare(strict_types=1);

namespace SimplePayment\Core\Domain;

use SimplePayment\Core\Domain\Exception\InsufficientBalance;

class Wallet
{
    private float $amount;

    private function __construct(float $amount)
    {
        $this->amount = $amount;
    }

    public static function create(float $amount): self
    {
        return new self($amount);
    }

    public function deposit(float $amount): void
    {
        $this->amount += $amount;
    }

    /**
     * @throws InsufficientBalance
     */
    public function withdraw(float $amount): void
    {
        if ($this->amount < $amount) {
            throw InsufficientBalance::forWithdraw();
        }

        $this->amount -= $amount;
    }

    public function amount(): float
    {
        return $this->amount;
    }
}
