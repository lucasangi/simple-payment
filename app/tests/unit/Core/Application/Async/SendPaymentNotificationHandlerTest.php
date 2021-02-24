<?php

declare(strict_types=1);

namespace SimplePayment\Tests\Core\Application\Async;

use PHPUnit\Framework\TestCase;
use SimplePayment\Core\Application\Async\SendPaymentNotification;
use SimplePayment\Core\Application\Async\SendPaymentNotificationHandler;
use SimplePayment\Core\Domain\Notifier;

use function assert;

class SendPaymentNotificationHandlerTest extends TestCase
{
    public function testHandlerShouldSendNotification(): void
    {
        $mockNotifier = $this->getMockBuilder(Notifier::class)
            ->onlyMethods(['notify'])
            ->getMock();

        $mockNotifier->expects($this->once())->method('notify');

        assert($mockNotifier instanceof Notifier);

        $handler = new SendPaymentNotificationHandler($mockNotifier);

        $command = new SendPaymentNotification('c4e354a0-6a16-465d-8ed9-3f47133784e8', 122.56);

        $this->assertEquals('c4e354a0-6a16-465d-8ed9-3f47133784e8', $command->userId);
        $this->assertEquals(122.56, $command->amount);

        $handler($command);
    }
}
