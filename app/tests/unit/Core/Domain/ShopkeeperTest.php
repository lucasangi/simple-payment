<?php

declare(strict_types=1);

namespace SimplePayment\Tests\Core\Domain;

use PHPUnit\Framework\TestCase;
use SimplePayment\Core\Domain\Event\PaymentReceived;
use SimplePayment\Core\Domain\Shopkeeper;

use function assert;
use function method_exists;

class ShopkeeperTest extends TestCase
{
    public function testShouldCreateAShopkeeper(): void
    {
        $shopkeeper = Shopkeeper::create(
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
    }

    public function testShouldDepositAmountForShopkeeper(): void
    {
        $shopkeeper = Shopkeeper::create(
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
    }

    public function testShopkeeperShouldNotCanMakeTransactions(): void
    {
        $shopkeeper = Shopkeeper::create(
            'Carlos Eduardo e Lavínia Buffet ME',
            '75.778.772/0001-58',
            'almoxarifado@carloseduardoelaviniabuffetme.com.br',
            '53HBej7K',
            1000
        );

        $this->assertNotTrue(method_exists($shopkeeper, 'transferAmountToAccount'));
    }
}
