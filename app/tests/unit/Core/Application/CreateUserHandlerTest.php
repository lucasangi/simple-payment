<?php

declare(strict_types=1);

namespace SimplePayment\Tests\Core\Application;

use PHPUnit\Framework\TestCase;
use SimplePayment\Core\Application\CreateUser;
use SimplePayment\Core\Application\CreateUserHandler;
use SimplePayment\Core\Application\Exception\InvalidUser;
use SimplePayment\Core\Domain\CommonUser;
use SimplePayment\Core\Infrastructure\Persistence\InMemoryUserRepository;
use SimplePayment\Framework\Id\Domain\Id;
use SimplePayment\Framework\PasswordEncoder\Infrastructure\NativePasswordEncoder;

class CreateUserHandlerTest extends TestCase
{
    /** @dataProvider userAttributesProvider */
    public function testShouldCreateUser(
        string $fullName,
        string $cpfOrCnpj,
        string $email,
        string $password,
        float $amount,
        string $type
    ): void {
        $repository = new InMemoryUserRepository([]);
        $passwordEncoder = new NativePasswordEncoder();

        $handler = new CreateUserHandler($repository, $passwordEncoder);

        $command = new CreateUser(
            $fullName,
            $cpfOrCnpj,
            $email,
            $password,
            $amount,
            $type
        );

        $createdUserId = $handler($command);

        $createdUser = $repository->findOneById($createdUserId);

        $this->assertEquals($fullName, $createdUser->fullName());
        $this->assertEquals($cpfOrCnpj, $createdUser->cpfOrCnpj());
        $this->assertEquals($email, $createdUser->email());
        $this->assertEquals($amount, $createdUser->walletAmount());
    }

    /** @dataProvider userAttributesProvider */
    public function testShouldThrowExceptionWhenGivenEmailAlreadyExists(
        string $fullName,
        string $cpfOrCnpj,
        string $email,
        string $password,
        float $amount,
        string $type
    ): void {
        $this->expectException(InvalidUser::class);

        $existingUser = CommonUser::create(
            Id::generate(),
            'Leandro Eduardo Luan Costa',
            '122.004.920-49',
            $email,
            '8G4QM9qJOs',
            0
        );

        $repository = new InMemoryUserRepository([$existingUser]);

        $passwordEncoder = new NativePasswordEncoder();

        $handler = new CreateUserHandler($repository, $passwordEncoder);

        $command = new CreateUser(
            $fullName,
            $cpfOrCnpj,
            $email,
            $password,
            $amount,
            $type
        );

        $handler($command);
    }

    /** @dataProvider userAttributesProvider */
    public function testShouldThrowExceptionWhenGivenCPForCPNJAlreadyExists(
        string $fullName,
        string $cpfOrCnpj,
        string $email,
        string $password,
        float $amount,
        string $type
    ): void {
        $this->expectException(InvalidUser::class);

        $existingUser = CommonUser::create(
            Id::generate(),
            'Leandro Eduardo Luan Costa',
            $cpfOrCnpj,
            'leandroeduardoluancosta-98@tirel.com.br',
            '8G4QM9qJOs',
            0
        );

        $repository = new InMemoryUserRepository([$existingUser]);
        $passwordEncoder = new NativePasswordEncoder();

        $handler = new CreateUserHandler($repository, $passwordEncoder);

        $command = new CreateUser(
            $fullName,
            $cpfOrCnpj,
            $email,
            $password,
            $amount,
            $type
        );

        $handler($command);
    }

    public function testShouldThrowExceptionWhenGivenUserTypeNotExists(): void
    {
        $this->expectException(InvalidUser::class);

        $repository = new InMemoryUserRepository([]);
        $passwordEncoder = new NativePasswordEncoder();

        $handler = new CreateUserHandler($repository, $passwordEncoder);

        $command = new CreateUser(
            'Evelyn Fernanda Gomes',
            '130.029.570-89',
            'evelynfernandagomes-86@gilconsultoria.com.br',
            'JExOGEJq0P',
            500,
            'invalid-type'
        );

        $handler($command);
    }

    /** @return array<mixed> */
    public function userAttributesProvider(): array
    {
        return [
            'Common User' => [
                'Evelyn Fernanda Gomes',
                '130.029.570-89',
                'evelynfernandagomes-86@gilconsultoria.com.br',
                'JExOGEJq0P',
                500,
                'common',
            ],
            'Shopkeeper' => [
                'Carlos Eduardo e Lavínia Buffet ME',
                '75.778.772/0001-58',
                'almoxarifado@carloseduardoelaviniabuffetme.com.br',
                '53HBej7K',
                0,
                'shopkeeper',
            ],
        ];
    }
}
