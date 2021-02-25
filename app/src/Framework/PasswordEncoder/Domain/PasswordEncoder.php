<?php

declare(strict_types=1);

namespace SimplePayment\Framework\PasswordEncoder\Domain;

interface PasswordEncoder
{
    public function encodePassword(string $password): string;
}
