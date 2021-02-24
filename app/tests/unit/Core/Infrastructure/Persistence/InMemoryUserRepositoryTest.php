<?php

declare(strict_types=1);

namespace SimplePayment\Tests\Core\Infrastructure\Persistence;

use PHPUnit\Framework\TestCase;
use SimplePayment\Core\Domain\CommonUser;
use SimplePayment\Core\Domain\Exception\UserNotFound;
use SimplePayment\Core\Domain\Shopkeeper;
use SimplePayment\Core\Domain\User;
use SimplePayment\Core\Infrastructure\Persistence\InMemoryUserRepository;
use SimplePayment\Framework\Id\Domain\Id;

use function assert;

class InMemoryUserRepositoryTest extends TestCase
{
    /** @dataProvider userProvider */
    public function testShouldSaveUser(User $user): void
    {
        $repository = new InMemoryUserRepository([]);

        $repository->save($user);

        $foundedUser = $repository->findOneById($user->id());

        $this->assertEquals($user->fullName(), $foundedUser->fullName());
        $this->assertEquals($user->email(), $foundedUser->email());
        $this->assertEquals($user->cpfOrCnpj(), $foundedUser->cpfOrCnpj());
        $this->assertEquals($user->password(), $foundedUser->password());
        $this->assertEquals($user->walletAmount(), $foundedUser->walletAmount());
    }

    /** @dataProvider userProvider */
    public function testShouldUpdateUser(User $user): void
    {
        $repository = new InMemoryUserRepository([]);

        $repository->save($user);

        $editedUser = $repository->findOneById($user->id());

        $editedUser->receive(200);

        $repository->save($editedUser);

        $foundedUser = $repository->findOneById($user->id());
        $this->assertEquals(200, $foundedUser->walletAmount());
    }

    public function testShouldThrowExceptionWhenNotFoundUserById(): void
    {
        $this->expectException(UserNotFound::class);

        $repository = new InMemoryUserRepository([]);

        $repository->findOneById(Id::generate());
    }

    public function testShouldFindUserByEmail(): void
    {
        $user = CommonUser::create(
            Id::generate(),
            'Leandro Eduardo Luan Costa',
            '122.004.920-49',
            'leandroeduardoluancosta-98@tirel.com.br',
            '8G4QM9qJOs',
            0
        );

        $repository = new InMemoryUserRepository([$user]);

        $foundedUser = $repository->findOneOrNullByEmail('leandroeduardoluancosta-98@tirel.com.br');

        assert($foundedUser instanceof User);
        $this->assertNotNull($foundedUser);
    }

    public function testShouldReturnNullWhenNotFindUserByEmail(): void
    {
        $repository = new InMemoryUserRepository([]);

        $foundedUser = $repository->findOneOrNullByEmail('example@example.com');

        $this->assertNull($foundedUser);
    }

    public function testShouldFindUserByCpfOrCnpj(): void
    {
        $user = CommonUser::create(
            Id::generate(),
            'Leandro Eduardo Luan Costa',
            '122.004.920-49',
            'leandroeduardoluancosta-98@tirel.com.br',
            '8G4QM9qJOs',
            0
        );

        $repository = new InMemoryUserRepository([$user]);

        $foundedUser = $repository->findOneOrNullByCpfOrCnpj('122.004.920-49');

        assert($foundedUser instanceof User);
        $this->assertNotNull($foundedUser);
    }

    public function testShouldReturnNullWhenNotFindUserByCpfOrCnpj(): void
    {
        $repository = new InMemoryUserRepository([]);

        $foundedUser = $repository->findOneOrNullByEmail('75.778.772/0001-58');

        $this->assertNull($foundedUser);
    }

    /** @return array<mixed> */
    public function userProvider(): array
    {
        return [
            'Common User' => [
                CommonUser::create(
                    Id::generate(),
                    'Leandro Eduardo Luan Costa',
                    '122.004.920-49',
                    'leandroeduardoluancosta-98@tirel.com.br',
                    '8G4QM9qJOs',
                    0
                ),
            ],
            'Shopkeeper' => [
                Shopkeeper::create(
                    Id::generate(),
                    'Carlos Eduardo e Lavínia Buffet ME',
                    '75.778.772/0001-58',
                    'almoxarifado@carloseduardoelaviniabuffetme.com.br',
                    '53HBej7K',
                    0
                ),
            ],
        ];
    }
}
