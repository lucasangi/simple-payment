<?php

declare(strict_types=1);

namespace SimplePayment\Core\Domain;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class CommonUser extends UserWithTransactionOption
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
