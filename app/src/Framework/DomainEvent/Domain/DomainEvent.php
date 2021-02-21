<?php

declare(strict_types=1);

namespace SimplePayment\Framework\DomainEvent\Domain;

use DateTimeImmutable;

interface DomainEvent
{
    public function occurredOn(): DateTimeImmutable;
}
