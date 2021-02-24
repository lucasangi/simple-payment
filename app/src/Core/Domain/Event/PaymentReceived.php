<?php

declare(strict_types=1);

namespace SimplePayment\Core\Domain\Event;

use DateTimeImmutable;
use SimplePayment\Framework\DomainEvent\Domain\DomainEvent;

class PaymentReceived implements DomainEvent
{
    private string $userId;
    private float $amount;
    private DateTimeImmutable $occurredOn;

    private function __construct(string $userId, float $amount)
    {
        $this->amount = $amount;
        $this->userId = $userId;
        $this->occurredOn = new DateTimeImmutable();
    }

    public function userId(): string
    {
        return $this->userId;
    }

    public function amount(): float
    {
        return $this->amount;
    }

    public function occurredOn(): DateTimeImmutable
    {
        return $this->occurredOn;
    }

    public static function create(string $userId, float $amount): self
    {
        return new self($userId, $amount);
    }
}
