<?php

declare(strict_types=1);

namespace SimplePayment\Core\Application\Async;

use SimplePayment\Core\Domain\Notifier;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class SendPaymentNotificationHandler implements MessageHandlerInterface
{
    private Notifier $notifier;

    public function __construct(Notifier $notifier)
    {
        $this->notifier = $notifier;
    }

    public function __invoke(SendPaymentNotification $command): void
    {
        $this->notifier->notify();
    }
}
