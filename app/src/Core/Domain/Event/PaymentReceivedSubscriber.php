<?php

declare(strict_types=1);

namespace SimplePayment\Core\Domain\Event;

use SimplePayment\Core\Application\Async\SendPaymentNotification;
use SimplePayment\Framework\DomainEvent\Domain\DomainEvent;
use SimplePayment\Framework\DomainEvent\Domain\DomainEventSubscriber;
use Symfony\Component\Messenger\MessageBusInterface;

use function assert;

class PaymentReceivedSubscriber implements DomainEventSubscriber
{
    private MessageBusInterface $authlessBus;

    public function __construct(MessageBusInterface $authlessBus)
    {
        $this->authlessBus = $authlessBus;
    }

    /**
     * @inheritDoc
     */
    public function subscribedEvents(): array
    {
        return [PaymentReceived::class];
    }

    public function handle(DomainEvent $domainEvent): void
    {
        assert($domainEvent instanceof PaymentReceived);

        $command = new SendPaymentNotification($domainEvent->userId(), $domainEvent->amount());

        $this->authlessBus->dispatch($command);
    }
}
