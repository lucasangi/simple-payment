<?php

declare(strict_types=1);

namespace SimplePayment\Tests\Core\Domain\Exception;

use PHPUnit\Framework\TestCase;
use SimplePayment\Core\Domain\Exception\UserNotFound;

class UserNotFoundTest extends TestCase
{
    public function testShouldThrowExceptionWhenNotFoundUserWithGivenId(): void
    {
        $exception = UserNotFound::withGivenId(1);

        $this->assertEquals('No user was found with the given id.', $exception->getMessage());
        $this->assertEquals('User not found.', $exception->getTitle());
        $this->assertEquals(['id' => 1], $exception->getExtraDetails());
    }
}
