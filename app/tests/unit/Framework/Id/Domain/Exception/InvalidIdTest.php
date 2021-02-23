<?php

declare(strict_types=1);

namespace SimplePayment\Tests\Framework\Id\Domain\Exception;

use PHPUnit\Framework\TestCase;
use SimplePayment\Framework\Id\Domain\Exception\InvalidId;

use function sprintf;

class InvalidIdTest extends TestCase
{
    public function testShouldThrowExceptionWhenGivenIdIsInvalid(): void
    {
        $exception = InvalidId::invalidIdString('invalid-id');

        $expectedMessage = sprintf('Invalid UUID string: %s.', 'invalid-id');

        $this->assertEquals($expectedMessage, $exception->getMessage());
        $this->assertEquals('Invalid id.', $exception->getTitle());
    }
}
