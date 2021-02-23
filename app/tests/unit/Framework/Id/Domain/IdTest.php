<?php

declare(strict_types=1);

namespace SimplePayment\Tests\Framework\Id\Domain;

use PHPUnit\Framework\TestCase;
use SimplePayment\Framework\Id\Domain\Exception\InvalidId;
use SimplePayment\Framework\Id\Domain\Id;

class IdTest extends TestCase
{
    public function testShouldGenerateId(): void
    {
        $id = Id::generate();

        $this->assertNotEmpty($id->toString());
    }

    public function testShouldConstructIdFromString(): void
    {
        $anId = Id::fromString('248ca0a5-071e-4276-9663-022e66fecf02');

        $this->assertEquals($anId->toString(), '248ca0a5-071e-4276-9663-022e66fecf02');
    }

    public function testShouldThrowExceptionWhenGivenIdIsInvalid(): void
    {
        $this->expectException(InvalidId::class);
        $anId = Id::fromString('id-example');

        $this->assertEquals($anId->toString(), '248ca0a5-id-example');
    }

    public function testShouldCompareEqualsIds(): void
    {
        $anId = Id::fromString('fec66291-f2ff-4c92-b16e-f0a2b61414d8');
        $anotherId = Id::fromString('fec66291-f2ff-4c92-b16e-f0a2b61414d8');

        $this->assertEquals($anId->toString(), $anotherId->toString());
    }

    public function testShouldCompareDifferentIds(): void
    {
        $anId = Id::fromString('480040bc-ff7e-425b-84d7-e239fe7ce88e');
        $anotherId = Id::fromString('e7822f11-eaa9-4373-8521-bd1ae63b385e');

        $this->assertNotEquals($anId->toString(), $anotherId->toString());
    }
}
