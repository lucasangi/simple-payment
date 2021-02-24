<?php

declare(strict_types=1);

namespace SimplePayment\Tests\Core\Domain\Event;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use SimplePayment\Core\Domain\Event\PaymentReceived;

class PaymentReceivedTest extends TestCase
{
    public function testShouldCreatePaymentReceivedEvent(): void
    {
        $event = PaymentReceived::create('41f715cb-c411-4b5a-8e12-340bfe3f9b62', 100);

        $this->assertEquals(100, $event->amount());
        $this->assertEquals('41f715cb-c411-4b5a-8e12-340bfe3f9b62', $event->userId());
        $this->assertInstanceOf(DateTimeImmutable::class, $event->occurredOn());
    }
}
