<?php

declare(strict_types=1);

namespace SimplePayment\Core\Domain;

class User extends AccountWithTransactionOption
{
    public static function create(
        string $fullName,
        string $cpf,
        string $email,
        string $password,
        float $amount
    ): self {
        return new self(
            $fullName,
            $cpf,
            $email,
            $password,
            $amount
        );
    }
}
