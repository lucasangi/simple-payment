<?php

declare(strict_types=1);

namespace SimplePayment\Framework\DomainEvent\Domain;

use function get_class;
use function in_array;

class DomainEventPublisher
{
    /** @var DomainEventSubscriber[] $subscribers */
    private array $subscribers;

    /** @param DomainEventSubscriber[] $subscribers */
    public function __construct(array $subscribers)
    {
        $this->subscribers = $subscribers;
    }

    public function publish(DomainEvent $event): void
    {
        foreach ($this->subscribers as $subscriber) {
            if (! $this->subscriberSupportsEvent($subscriber, $event)) {
                continue;
            }

            $subscriber->handle($event);
        }
    }

    private function subscriberSupportsEvent(DomainEventSubscriber $subscriber, DomainEvent $event): bool
    {
        $eventClass = get_class($event);

        return in_array($eventClass, $subscriber->subscribedEvents());
    }
}
