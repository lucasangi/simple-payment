<?php

declare(strict_types=1);

namespace SimplePayment\Tests\Core\Domain;

use PHPUnit\Framework\TestCase;
use SimplePayment\Core\Domain\CommonUser;
use SimplePayment\Core\Domain\Event\PaymentReceived;
use SimplePayment\Framework\Id\Domain\Id;

use function assert;

class CommonUserTest extends TestCase
{
    public function testShouldCreateACommonUser(): void
    {
        $commomUser = CommonUser::create(
            Id::fromString('86b708c6-8b0e-40d4-adf2-06894472179c'),
            'Evelyn Fernanda Gomes',
            '130.029.570-89',
            'evelynfernandagomes-86@gilconsultoria.com.br',
            'JExOGEJq0P',
            100
        );

        $this->assertEquals('86b708c6-8b0e-40d4-adf2-06894472179c', $commomUser->id());
        $this->assertEquals('Evelyn Fernanda Gomes', $commomUser->fullName());
        $this->assertEquals('130.029.570-89', $commomUser->cpfOrCnpj());
        $this->assertEquals('evelynfernandagomes-86@gilconsultoria.com.br', $commomUser->email());
        $this->assertEquals('JExOGEJq0P', $commomUser->password());
        $this->assertEquals(100, $commomUser->walletAmount());
        $this->assertEquals([], $commomUser->domainEvents());
    }

    public function testShouldDepositAmountForCommonUser(): void
    {
        $commomUser = CommonUser::create(
            Id::fromString('3a80395b-f8fa-4789-8c10-e91b2fcc4cb4'),
            'Evelyn Fernanda Gomes',
            '130.029.570-89',
            'evelynfernandagomes-86@gilconsultoria.com.br',
            'JExOGEJq0P',
            0
        );

        $commomUser->receive(100);

        $this->assertEquals(100, $commomUser->walletAmount());

        $storedDomainEvents = $commomUser->domainEvents();

        $this->assertCount(1, $storedDomainEvents);
        $this->assertCount(0, $commomUser->domainEvents());

        [$paymentReceivedEvent] = $storedDomainEvents;

        assert($paymentReceivedEvent instanceof PaymentReceived);

        $this->assertInstanceOf(PaymentReceived::class, $paymentReceivedEvent);
        $this->assertEquals(100, $paymentReceivedEvent->amount());
        $this->assertEquals('3a80395b-f8fa-4789-8c10-e91b2fcc4cb4', $paymentReceivedEvent->userId());
    }

    public function testShouldTransferAmountFromCommonUserToAnother(): void
    {
        $payer = CommonUser::create(
            Id::generate(),
            'Evelyn Fernanda Gomes',
            '130.029.570-89',
            'evelynfernandagomes-86@gilconsultoria.com.br',
            'JExOGEJq0P',
            100
        );

        $payee = CommonUser::create(
            Id::generate(),
            'Leandro Eduardo Luan Costa',
            '122.004.920-49',
            'leandroeduardoluancosta-98@tirel.com.br',
            '8G4QM9qJOs',
            33
        );

        $payer->transferAmountToUser(67, $payee);

        $this->assertEquals(33, $payer->walletAmount());
        $this->assertEquals(100, $payee->walletAmount());

        $storedDomainEvents = $payee->domainEvents();

        $this->assertCount(1, $storedDomainEvents);

        [$paymentReceivedEvent] = $storedDomainEvents;

        assert($paymentReceivedEvent instanceof PaymentReceived);
        $this->assertInstanceOf(PaymentReceived::class, $paymentReceivedEvent);
        $this->assertEquals(67, $paymentReceivedEvent->amount());
        $this->assertEquals($payee->id(), $paymentReceivedEvent->userId());
    }
}
