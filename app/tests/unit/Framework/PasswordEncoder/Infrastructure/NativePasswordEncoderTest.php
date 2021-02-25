<?php

declare(strict_types=1);

namespace SimplePayment\Tests\Framework\PasswordEncoder\Infrastructure;

use PHPUnit\Framework\TestCase;
use SimplePayment\Framework\PasswordEncoder\Infrastructure\NativePasswordEncoder;

use function password_verify;

class NativePasswordEncoderTest extends TestCase
{
    public function testShouldEncodePassword(): void
    {
        $password = 'example password';

        $passwordEncoder = new NativePasswordEncoder();

        $encodedPassword = $passwordEncoder->encodePassword($password);

        $this->assertTrue(password_verify($password, $encodedPassword));
    }
}
