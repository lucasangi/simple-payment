<?php

declare(strict_types=1);

namespace SimplePayment\Tests\Core\Domain\Exception;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use PHPUnit\Framework\TestCase;
use SimplePayment\Core\Domain\Exception\AuthenticationFailed;

class AuthenticationFailedTest extends TestCase
{
    public function testShouldThrowExceptionWhenAuthenticationFailed(): void
    {
        $previousExceptionTitle = 'Error Communicating with Server';
        $previousExceptionRequest = new Request('GET', 'example.com');
        $previousException = new RequestException($previousExceptionTitle, $previousExceptionRequest);

        $exception = AuthenticationFailed::fromAuthenticationError($previousException);

        $this->assertEquals('The authentication request failed.', $exception->getMessage());
        $this->assertEquals('Authentication Failed.', $exception->getTitle());
        $this->assertEquals($previousException, $exception->getPrevious());
    }
}
