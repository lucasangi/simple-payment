<?php

declare(strict_types=1);

namespace SimplePayment\Tests\Core\Domain;

use PHPUnit\Framework\TestCase;
use SimplePayment\Core\Domain\Event\PaymentReceived;
use SimplePayment\Core\Domain\Shopkeeper;
use SimplePayment\Framework\Id\Domain\Id;

use function assert;
use function method_exists;

class ShopkeeperTest extends TestCase
{
    public function testShouldCreateAShopkeeper(): void
    {
        $shopkeeper = Shopkeeper::create(
            Id::fromString('62018058-e779-4172-b47f-72ae9c03e1ed'),
            'Carlos Eduardo e Lavínia Buffet ME',
            '75.778.772/0001-58',
            'almoxarifado@carloseduardoelaviniabuffetme.com.br',
            '53HBej7K',
            1000
        );

        $this->assertEquals('Carlos Eduardo e Lavínia Buffet ME', $shopkeeper->fullName());
        $this->assertEquals('75.778.772/0001-58', $shopkeeper->cpfOrCnpj());
        $this->assertEquals('almoxarifado@carloseduardoelaviniabuffetme.com.br', $shopkeeper->email());
        $this->assertEquals('53HBej7K', $shopkeeper->password());
        $this->assertEquals(1000, $shopkeeper->walletAmount());
        $this->assertEquals([], $shopkeeper->domainEvents());
        $this->assertEquals('shopkeeper', $shopkeeper->type());
    }

    public function testShouldDepositAmountForShopkeeper(): void
    {
        $shopkeeper = Shopkeeper::create(
            Id::fromString('bfcd0f5c-8c04-46bf-a5c7-c0ed30323259'),
            'Carlos Eduardo e Lavínia Buffet ME',
            '75.778.772/0001-58',
            'almoxarifado@carloseduardoelaviniabuffetme.com.br',
            '53HBej7K',
            1000
        );

        $shopkeeper->receive(100);

        $this->assertEquals(1100, $shopkeeper->walletAmount());

        $storedDomainEvents = $shopkeeper->domainEvents();

        $this->assertCount(1, $storedDomainEvents);
        $this->assertCount(0, $shopkeeper->domainEvents());

        [$paymentReceivedEvent] = $storedDomainEvents;

        assert($paymentReceivedEvent instanceof PaymentReceived);
        $this->assertInstanceOf(PaymentReceived::class, $paymentReceivedEvent);
        $this->assertEquals(100, $paymentReceivedEvent->amount());
        $this->assertEquals('bfcd0f5c-8c04-46bf-a5c7-c0ed30323259', $paymentReceivedEvent->userId());
    }

    public function testShopkeeperShouldNotCanMakeTransactions(): void
    {
        $shopkeeper = Shopkeeper::create(
            Id::generate(),
            'Carlos Eduardo e Lavínia Buffet ME',
            '75.778.772/0001-58',
            'almoxarifado@carloseduardoelaviniabuffetme.com.br',
            '53HBej7K',
            1000
        );

        $this->assertNotTrue(method_exists($shopkeeper, 'transferAmountToUser'));
    }
}
