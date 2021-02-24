<?php

declare(strict_types=1);

namespace SimplePayment\Tests\Core\Application;

use PHPUnit\Framework\TestCase;
use SimplePayment\Core\Application\Exception\InvalidTransaction;
use SimplePayment\Core\Application\TransferMoney;
use SimplePayment\Core\Application\TransferMoneyHandler;
use SimplePayment\Core\Domain\CommonUser;
use SimplePayment\Core\Domain\Shopkeeper;
use SimplePayment\Core\Domain\User;
use SimplePayment\Core\Infrastructure\Persistence\InMemoryUserRepository;
use SimplePayment\Framework\Id\Domain\Id;

class TransferMoneyHandlerTest extends TestCase
{
    /** @dataProvider transactionProvider */
    public function testShouldTransferMoneyBetweenUsers(
        User $payer,
        User $payee,
        float $value
    ): void {
        $repository = new InMemoryUserRepository([$payee, $payer]);

        $handler = new TransferMoneyHandler($repository);

        $command = new TransferMoney(
            $value,
            $payer->id()->toString(),
            $payee->id()->toString()
        );

        $this->assertEquals($value, $command->value);
        $this->assertEquals($payer->id(), $command->payer);
        $this->assertEquals($payee->id(), $command->payee);

        $handler($command);

        $this->assertEquals(0, $payer->walletAmount());
        $this->assertEquals(500, $payee->walletAmount());
    }

    public function testShouldThrowExceptionWhenTryTransferMoneyWithShopkeeperAsPayer(): void
    {
        $this->expectException(InvalidTransaction::class);

        $payee = CommonUser::create(
            Id::generate(),
            'Evelyn Fernanda Gomes',
            '130.029.570-89',
            'evelynfernandagomes-86@gilconsultoria.com.br',
            'JExOGEJq0P',
            0
        );

        $payer = Shopkeeper::create(
            Id::generate(),
            'Carlos Eduardo e Lavínia Buffet ME',
            '75.778.772/0001-58',
            'almoxarifado@carloseduardoelaviniabuffetme.com.br',
            '53HBej7K',
            500
        );

        $value = 500;

        $repository = new InMemoryUserRepository([$payee, $payer]);

        $handler = new TransferMoneyHandler($repository);

        $command = new TransferMoney(
            $value,
            $payer->id()->toString(),
            $payee->id()->toString()
        );

        $handler($command);
    }

    /** @return array<mixed> */
    public function transactionProvider(): array
    {
        return [
            'From Common User To Shopkeeper' => [
                CommonUser::create(
                    Id::generate(),
                    'Evelyn Fernanda Gomes',
                    '130.029.570-89',
                    'evelynfernandagomes-86@gilconsultoria.com.br',
                    'JExOGEJq0P',
                    500
                ),
                Shopkeeper::create(
                    Id::generate(),
                    'Carlos Eduardo e Lavínia Buffet ME',
                    '75.778.772/0001-58',
                    'almoxarifado@carloseduardoelaviniabuffetme.com.br',
                    '53HBej7K',
                    0
                ),
                500,
            ],
            'From Common User To Another' => [
                CommonUser::create(
                    Id::generate(),
                    'Evelyn Fernanda Gomes',
                    '130.029.570-89',
                    'evelynfernandagomes-86@gilconsultoria.com.br',
                    'JExOGEJq0P',
                    500
                ),
                CommonUser::create(
                    Id::generate(),
                    'Leandro Eduardo Luan Costa',
                    '122.004.920-49',
                    'leandroeduardoluancosta-98@tirel.com.br',
                    '8G4QM9qJOs',
                    0
                ),
                500,
            ],
        ];
    }
}
