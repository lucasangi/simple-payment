<?php

declare(strict_types=1);

namespace SimplePayment\Tests\Core\Application\Exception;

use PHPUnit\Framework\TestCase;
use SimplePayment\Core\Application\Exception\InvalidUser;

use function sprintf;

class InvalidUserTest extends TestCase
{
    public function testShouldThrowExceptionWhenGivenUserTypeNotExists(): void
    {
        $exception = InvalidUser::fromInvalidUserType('invalid-type');

        $expectedMessage = sprintf('The given user type (%s) not exists.', 'invalid-type');

        $this->assertEquals($expectedMessage, $exception->getMessage());
        $this->assertEquals('Invalid User.', $exception->getTitle());
    }

    public function testShouldThrowExceptionWhenGivenCPForCPNJAlreadyExists(): void
    {
        $exception = InvalidUser::fromExistingEmail('example@example.com');

        $expectedMessage = sprintf('The given email (%s) already exists.', 'example@example.com');

        $this->assertEquals($expectedMessage, $exception->getMessage());
        $this->assertEquals('Invalid User.', $exception->getTitle());
    }

    public function testShouldThrowExceptionWhenGivenEmailAlreadyExists(): void
    {
        $exception = InvalidUser::fromExistingCPForCPNJ('92.215.900/0001-74');

        $expectedMessage = sprintf('The given CPF/CPNJ (%s) already exists.', '92.215.900/0001-74');

        $this->assertEquals($expectedMessage, $exception->getMessage());
        $this->assertEquals('Invalid User.', $exception->getTitle());
    }
}
