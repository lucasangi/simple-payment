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
        $event = PaymentReceived::create(100);

        $this->assertEquals(100, $event->amount());
        $this->assertInstanceOf(DateTimeImmutable::class, $event->occurredOn());
    }
}
