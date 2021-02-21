<?php

declare(strict_types=1);

namespace SimplePayment\Tests\Core\Domain;

use PHPUnit\Framework\TestCase;
use SimplePayment\Core\Domain\Event\PaymentReceived;
use SimplePayment\Core\Domain\User;

use function assert;

class UserTest extends TestCase
{
    public function testShouldCreateAUser(): void
    {
        $user = User::create(
            'Evelyn Fernanda Gomes',
            '130.029.570-89',
            'evelynfernandagomes-86@gilconsultoria.com.br',
            'JExOGEJq0P',
            100
        );

        $this->assertEquals('Evelyn Fernanda Gomes', $user->fullName());
        $this->assertEquals('130.029.570-89', $user->cpfOrCnpj());
        $this->assertEquals('evelynfernandagomes-86@gilconsultoria.com.br', $user->email());
        $this->assertEquals('JExOGEJq0P', $user->password());
        $this->assertEquals(100, $user->walletAmount());
        $this->assertEquals([], $user->domainEvents());
    }

    public function testShouldDepositAmountForUser(): void
    {
        $user = User::create(
            'Evelyn Fernanda Gomes',
            '130.029.570-89',
            'evelynfernandagomes-86@gilconsultoria.com.br',
            'JExOGEJq0P',
            0
        );

        $user->receive(100);

        $this->assertEquals(100, $user->walletAmount());

        $storedDomainEvents = $user->domainEvents();

        $this->assertCount(1, $storedDomainEvents);
        $this->assertCount(0, $user->domainEvents());

        [$paymentReceivedEvent] = $storedDomainEvents;

        assert($paymentReceivedEvent instanceof PaymentReceived);
        $this->assertInstanceOf(PaymentReceived::class, $paymentReceivedEvent);
        $this->assertEquals(100, $paymentReceivedEvent->amount());
    }

    public function testShouldTransferAmountFromUserToAnother(): void
    {
        $payer = User::create(
            'Evelyn Fernanda Gomes',
            '130.029.570-89',
            'evelynfernandagomes-86@gilconsultoria.com.br',
            'JExOGEJq0P',
            100
        );

        $payee = User::create(
            'Leandro Eduardo Luan Costa',
            '122.004.920-49',
            'leandroeduardoluancosta-98@tirel.com.br',
            '8G4QM9qJOs',
            33
        );

        $payer->transferAmountToAccount(67, $payee);

        $this->assertEquals(33, $payer->walletAmount());
        $this->assertEquals(100, $payee->walletAmount());

        $storedDomainEvents = $payee->domainEvents();

        $this->assertCount(1, $storedDomainEvents);

        [$paymentReceivedEvent] = $storedDomainEvents;

        assert($paymentReceivedEvent instanceof PaymentReceived);
        $this->assertInstanceOf(PaymentReceived::class, $paymentReceivedEvent);
        $this->assertEquals(67, $paymentReceivedEvent->amount());
    }
}
