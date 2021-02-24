<?php

declare(strict_types=1);

namespace SimplePayment\Tests\Core\Domain\Exception;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use PHPUnit\Framework\TestCase;
use SimplePayment\Core\Domain\Exception\FailedToSendNotification;

class FailedToSendNotificationTest extends TestCase
{
    public function testShouldThrowExceptionWhenNotificationCanNotBeSended(): void
    {
        $previousExceptionTitle = 'Error Communicating with Server';
        $previousExceptionRequest = new Request('GET', 'example.com');
        $previousException = new RequestException($previousExceptionTitle, $previousExceptionRequest);

        $exception = FailedToSendNotification::fromConnectionError($previousException);

        $this->assertEquals('An error occurred on send notification.', $exception->getMessage());
        $this->assertEquals('Notification sending failed.', $exception->getTitle());
        $this->assertEquals($previousException, $exception->getPrevious());
    }
}
