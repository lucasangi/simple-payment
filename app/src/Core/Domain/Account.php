<?php

declare(strict_types=1);

namespace SimplePayment\Core\Domain;

use SimplePayment\Core\Domain\Event\PaymentReceived;
use SimplePayment\Framework\DomainEvent\Domain\DomainEvent;

abstract class Account
{
    protected string $fullName;

    protected string $cpfOrCnpj;

    protected string $email;

    protected string $password;

    protected Wallet $wallet;

    /** @var DomainEvent[] $domainEvents */
    protected array $domainEvents;

    protected function __construct(
        string $fullName,
        string $cpfOrCnpj,
        string $email,
        string $password,
        float $amount
    ) {
        $this->fullName = $fullName;
        $this->cpfOrCnpj = $cpfOrCnpj;
        $this->email = $email;
        $this->password = $password;
        $this->wallet = Wallet::create($amount);
    }

     /**
      * @return DomainEvent[]
      */
    public function domainEvents(): array
    {
        if (empty($this->domainEvents)) {
            return [];
        }

        $storedDomainEvents = $this->domainEvents;
        $this->domainEvents = [];

        return $storedDomainEvents;
    }

    public function fullName(): string
    {
        return $this->fullName;
    }

    public function cpfOrCnpj(): string
    {
        return $this->cpfOrCnpj;
    }

    public function email(): string
    {
        return $this->email;
    }

    public function password(): string
    {
        return $this->password;
    }

    public function walletAmount(): float
    {
        return $this->wallet->amount();
    }

    public function receive(float $amount): void
    {
        $this->wallet->deposit($amount);

        $this->domainEvents[] = PaymentReceived::create($amount);
    }
}
