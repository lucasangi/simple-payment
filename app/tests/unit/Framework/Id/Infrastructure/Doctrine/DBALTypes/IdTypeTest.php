<?php

declare(strict_types=1);

namespace SimplePayment\Tests\Framework\Id\Infrastructure\Doctrine\DBALTypes;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use PHPUnit\Framework\TestCase;
use SimplePayment\Framework\Id\Domain\Id as ValueObject;
use SimplePayment\Framework\Id\Infrastructure\Doctrine\DBALTypes\IdType;

use function assert;

class IdTypeTest extends TestCase
{
    private AbstractPlatform $platform;

    private Type $type;

    public static function setUpBeforeClass(): void
    {
        Type::addType('id', IdType::class);
    }

    protected function setUp(): void
    {
        $mockPlatform = $this->getMockBuilder(AbstractPlatform::class)
            ->addMethods([])
            ->getMockForAbstractClass();

        assert($mockPlatform instanceof AbstractPlatform);

        $this->platform = $mockPlatform;

        $this->type = Type::getType('id');
    }

    public function testConvertAnInstanceOfValueObjectToDatabaseMustReturnANonEmptyString(): void
    {
        $id = ValueObject::fromString('c78b6cbb-a503-4eea-a059-5d7f8c71f37b');

        $actual = $this->type->convertToDatabaseValue($id, $this->platform);

        $this->assertEquals('c78b6cbb-a503-4eea-a059-5d7f8c71f37b', $actual);
    }

    public function testConvertNullValueToDatabaseMustReturnNull(): void
    {
        $value = $this->type->convertToDatabaseValue(null, $this->platform);

        $this->assertNull($value);
    }

    public function testConvertNonEmptyStringFromTheDatabaseMustReturnValueObject(): void
    {
        $id = $this->type->convertToPHPValue('80642b16-3a38-4037-a000-c870adfa17ab', $this->platform);
        assert($id instanceof ValueObject);
        $this->assertInstanceOf(ValueObject::class, $id);
        $this->assertTrue($id->isEqualTo(ValueObject::fromString('80642b16-3a38-4037-a000-c870adfa17ab')));
    }

    public function testConvertANullValueFromTheDatabaseMustReturnNull(): void
    {
        $value = $this->type->convertToPHPValue(null, $this->platform);

        $this->assertNull($value);
    }
}
