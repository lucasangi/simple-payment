<?php

declare(strict_types=1);

namespace SimplePayment\Core\Domain;

use Doctrine\ORM\Mapping as ORM;
use SimplePayment\Framework\Id\Domain\Id;

/**
 * @ORM\Entity()
 */
class Shopkeeper extends User
{
    public static function type(): string
    {
        return 'shopkeeper';
    }

    public static function create(
        Id $id,
        string $fullName,
        string $cpf,
        string $email,
        string $password,
        float $amount
    ): self {
        return new self(
            $id,
            $fullName,
            $cpf,
            $email,
            $password,
            $amount
        );
    }
}
