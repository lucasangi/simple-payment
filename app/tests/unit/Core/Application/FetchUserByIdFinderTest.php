<?php

declare(strict_types=1);

namespace SimplePayment\Tests\Core\Application;

use PHPUnit\Framework\TestCase;
use SimplePayment\Core\Application\FetchUserById;
use SimplePayment\Core\Application\FetchUserByIdFinder;
use SimplePayment\Core\Domain\CommonUser;
use SimplePayment\Core\Domain\Shopkeeper;
use SimplePayment\Core\Domain\User;
use SimplePayment\Core\Infrastructure\Persistence\InMemoryUserRepository;
use SimplePayment\Framework\Id\Domain\Id;

class FetchUserByIdFinderTest extends TestCase
{
    /** @dataProvider userProvider */
    public function testShouldFetchUserById(User $user): void
    {
        $repository = new InMemoryUserRepository([$user]);

        $finder = new FetchUserByIdFinder($repository);

        $command = new FetchUserById(
            $user->id()->toString()
        );

        $userAsArray = $finder($command);

        $expectedUserAsArray = [
            'id' => $user->id()->toString(),
            'full_name' => $user->fullName(),
            'email' => $user->email(),
            'cnpj_cpf' => $user->cpfOrCnpj(),
            'wallet_amount' => $user->walletAmount(),
            'type' => $user->type(),
        ];

        $this->assertEquals($expectedUserAsArray, $userAsArray);
    }

    /** @return array<mixed> */
    public function userProvider(): array
    {
        return [
            'Common User' => [
                CommonUser::create(
                    Id::generate(),
                    'Evelyn Fernanda Gomes',
                    '130.029.570-89',
                    'evelynfernandagomes-86@gilconsultoria.com.br',
                    'JExOGEJq0P',
                    500
                ),
            ],
            'Shopkeeper' => [
                Shopkeeper::create(
                    Id::generate(),
                    'Carlos Eduardo e Lavínia Buffet ME',
                    '75.778.772/0001-58',
                    'almoxarifado@carloseduardoelaviniabuffetme.com.br',
                    '53HBej7K',
                    0,
                ),
            ],
        ];
    }
}
