<?php

declare(strict_types=1);

namespace SimplePayment\Framework\DomainEvent\Domain;

interface DomainEventSubscriber
{
    /** @return DomainEvent[] */
    public function subscribedEvents(): array;

    public function handle(DomainEvent $domainEvent): void;
}
