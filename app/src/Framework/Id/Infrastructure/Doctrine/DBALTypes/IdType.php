<?php

declare(strict_types=1);

namespace SimplePayment\Framework\Id\Infrastructure\Doctrine\DBALTypes;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\GuidType;
use SimplePayment\Framework\Id\Domain\Id;

class IdType extends GuidType
{
    public const NAME = 'uuid';

    /**
     * {@inheritdoc}
     *
     * @param mixed $value
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): ?Id
    {
        if (empty($value)) {
            return null;
        }

        return Id::fromString($value);
    }

    /**
     * {@inheritdoc}
     *
     * @param mixed $value
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if (! $value instanceof Id) {
            return null;
        }

        return $value->toString();
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getName(): string
    {
        return self::NAME;
    }
}
