<?php

declare(strict_types=1);

namespace SimplePayment\Core\Domain\Event;

use DateTimeImmutable;
use SimplePayment\Framework\DomainEvent\Domain\DomainEvent;

class PaymentReceived implements DomainEvent
{
    private float $amount;
    private DateTimeImmutable $occurredOn;

    private function __construct(float $amount)
    {
        $this->amount = $amount;
        $this->occurredOn = new DateTimeImmutable();
    }

    public function amount(): float
    {
        return $this->amount;
    }

    public function occurredOn(): DateTimeImmutable
    {
        return $this->occurredOn;
    }

    public static function create(float $amount): self
    {
        return new self($amount);
    }
}
