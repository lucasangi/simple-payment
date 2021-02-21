<?php

declare(strict_types=1);

namespace SimplePayment\Tests\Framework\Exception\Infrastructure\Handler\Exception;

use Lcobucci\ErrorHandling\Problem\Detailed;
use Lcobucci\ErrorHandling\Problem\Forbidden;
use Lcobucci\ErrorHandling\Problem\Titled;
use Lcobucci\ErrorHandling\Problem\Typed;
use RuntimeException;

use function sprintf;

final class InsufficientBalance extends RuntimeException implements Forbidden, Typed, Titled, Detailed
{
    private int $currentBalance;

    public static function forPurchase(int $currentBalance, int $cost): self
    {
        $exception = new self(sprintf('Your current balance is %d, but that costs %d.', $currentBalance, $cost));
        $exception->currentBalance = $currentBalance;

        return $exception;
    }

    public function getTypeUri(): string
    {
        return 'https://example.com/probs/insuficient-balance';
    }

    public function getTitle(): string
    {
        return 'You do not have enough balance.';
    }

    /**
     * {@inheritDoc}
     *
     * @return array<string, int>
     */
    public function getExtraDetails(): array
    {
        return ['balance' => $this->currentBalance];
    }
}
