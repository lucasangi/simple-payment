<?php

declare(strict_types=1);

namespace SimplePayment\Framework\Tests\DomainEvent\Domain;

use PHPUnit\Framework\TestCase;
use SimplePayment\Framework\DomainEvent\Domain\DomainEvent;
use SimplePayment\Framework\DomainEvent\Domain\DomainEventPublisher;
use SimplePayment\Framework\DomainEvent\Domain\DomainEventSubscriber;

use function assert;

class DomainEventPublisherTest extends TestCase
{
    public function testShouldPublishDomainEventAndNotifyOnlyCompatibleSubscribers(): void
    {
        $compatibleSubscriberMock = $this->getMockBuilder(DomainEventSubscriber::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['subscribedEvents', 'handle'])
            ->getMock();

        $compatibleSubscriberMock->expects($this->once())
            ->method('subscribedEvents')
            ->willReturn(['DomainEvent']);

        $compatibleSubscriberMock->expects($this->once())
            ->method('handle');

        $incompatibleSubscriberMock = $this->getMockBuilder(DomainEventSubscriber::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['subscribedEvents', 'handle'])
            ->getMock();

        $incompatibleSubscriberMock->expects($this->once())
            ->method('subscribedEvents')
            ->willReturn(['AnotherDomainEvent']);

        $incompatibleSubscriberMock->expects($this->never())
            ->method('handle');

        assert($compatibleSubscriberMock instanceof DomainEventSubscriber);
        assert($incompatibleSubscriberMock instanceof DomainEventSubscriber);
        $subscribers = [$compatibleSubscriberMock, $incompatibleSubscriberMock];

        $publisher = new DomainEventPublisher($subscribers);

        $domainEventMock = $this->getMockBuilder(DomainEvent::class)
            ->setMockClassName('DomainEvent')
            ->getMock();

        assert($domainEventMock instanceof DomainEvent);

        $publisher->publish($domainEventMock);
    }
}
