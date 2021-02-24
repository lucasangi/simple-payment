<?php

declare(strict_types=1);

namespace SimplePayment\Tests\Core\Domain\Exception;

use PHPUnit\Framework\TestCase;
use SimplePayment\Core\Domain\Exception\UserNotFound;
use SimplePayment\Framework\Id\Domain\Id;

class UserNotFoundTest extends TestCase
{
    public function testShouldThrowExceptionWhenNotFoundUserWithGivenId(): void
    {
        $id = Id::generate();
        $exception = UserNotFound::withGivenId($id);

        $this->assertEquals('No user was found with the given id.', $exception->getMessage());
        $this->assertEquals('User not found.', $exception->getTitle());
        $this->assertEquals(['id' => $id->toString()], $exception->getExtraDetails());
    }
}
