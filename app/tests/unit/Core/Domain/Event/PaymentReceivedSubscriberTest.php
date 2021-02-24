<?php

declare(strict_types=1);

namespace SimplePayment\Tests\Core\Domain\Event;

use PHPUnit\Framework\TestCase;
use SimplePayment\Core\Application\Async\SendPaymentNotification;
use SimplePayment\Core\Domain\Event\PaymentReceived;
use SimplePayment\Core\Domain\Event\PaymentReceivedSubscriber;
use SimplePayment\Framework\DomainEvent\Domain\DomainEvent;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

use function assert;

class PaymentReceivedSubscriberTest extends TestCase
{
    public function testSubscriberShouldBeSubscribedForDomainEvents(): void
    {
        $busMock = $this->getMockBuilder(MessageBusInterface::class)
            ->onlyMethods(['dispatch'])
            ->getMock();

        assert($busMock instanceof MessageBusInterface);

        $subscriber = new PaymentReceivedSubscriber($busMock);

        $expectedSubscribedEvents = [PaymentReceived::class];

        $this->assertEquals($expectedSubscribedEvents, $subscriber->subscribedEvents());
    }

    public function testSubscriberShouldDispatchSendNotificationCommand(): void
    {
        $busMock = $this->getMockBuilder(MessageBusInterface::class)
            ->onlyMethods(['dispatch'])
            ->getMock();

        $dispatchedCommand = new SendPaymentNotification('c4e354a0-6a16-465d-8ed9-3f47133784e8', 122.56);

        $busMock->expects($this->once())->method('dispatch')
            ->with($dispatchedCommand)
            ->willReturn(new Envelope($dispatchedCommand));

        assert($busMock instanceof MessageBusInterface);

        $subscriber = new PaymentReceivedSubscriber($busMock);

        $domainEvent = $this->aDomainEvent();

        $subscriber->handle($domainEvent);
    }

    public function aDomainEvent(): DomainEvent
    {
        return PaymentReceived::create('c4e354a0-6a16-465d-8ed9-3f47133784e8', 122.56);
    }
}
