<?php

declare(strict_types=1);

namespace SimplePayment\Framework\PasswordEncoder\Infrastructure;

use SimplePayment\Framework\PasswordEncoder\Domain\PasswordEncoder;

use function assert;
use function is_string;
use function password_hash;

use const PASSWORD_DEFAULT;

class NativePasswordEncoder implements PasswordEncoder
{
    public function encodePassword(string $password): string
    {
        $hash = password_hash($password, PASSWORD_DEFAULT);

        assert(is_string($hash));

        return $hash;
    }
}
