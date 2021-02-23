<?php

declare(strict_types=1);

namespace SimplePayment\Framework\Id\Domain;

use Ramsey\Uuid\Exception\InvalidUuidStringException;
use Ramsey\Uuid\Uuid;
use SimplePayment\Framework\Id\Domain\Exception\InvalidId;

/**
 * @psalm-immutable
 */
final class Id
{
    private string $id;

    private function __construct(string $id)
    {
        $this->id = $id;
    }

    public static function fromString(string $idAsString): self
    {
        try {
            $id = Uuid::fromString($idAsString);

            return new self($id->toString());
        } catch (InvalidUuidStringException $exception) {
            throw InvalidId::invalidIdString($idAsString);
        }
    }

    public static function generate(): self
    {
        $id = Uuid::uuid4();

        return new self($id->toString());
    }

    public function toString(): string
    {
        return $this->id;
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    public function isEqualTo(Id $id): bool
    {
        return $this->toString() === $id->toString();
    }
}
