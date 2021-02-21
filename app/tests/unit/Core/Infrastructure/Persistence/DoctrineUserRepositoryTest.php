<?php

declare(strict_types=1);

namespace SimplePayment\Tests\Core\Infrastructure\Persistence;

use SimplePayment\Core\Domain\CommonUser;
use SimplePayment\Core\Domain\Exception\UserNotFound;
use SimplePayment\Core\Domain\Shopkeeper;
use SimplePayment\Core\Domain\User;
use SimplePayment\Core\Infrastructure\Persistence\DoctrineUserRepository;
use SimplePayment\Framework\DomainEvent\Domain\DomainEventPublisher;
use SimplePayment\Tests\Framework\DoctrineTestCase;

use function assert;

class DoctrineUserRepositoryTest extends DoctrineTestCase
{
    /** @dataProvider userProvider */
    public function testShouldSaveUser(User $user): void
    {
        $mockPublisher = $this->getMockBuilder(DomainEventPublisher::class)
            ->disableOriginalConstructor()
            ->getMock();

        assert($mockPublisher instanceof DomainEventPublisher);

        $repository = new DoctrineUserRepository($this->entityManager, $mockPublisher);

        $repository->save($user);
        $this->entityManager->flush();
        $this->entityManager->clear();

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
        $mockPublisher = $this->getMockBuilder(DomainEventPublisher::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['publish'])
            ->getMock();

        assert($mockPublisher instanceof DomainEventPublisher);

        $mockPublisher->expects($this->once())->method('publish');

        $repository = new DoctrineUserRepository($this->entityManager, $mockPublisher);

        $repository->save($user);
        $this->entityManager->flush();
        $this->entityManager->clear();

        $editedUser = $repository->findOneById($user->id());

        $editedUser->receive(200);

        $repository->save($editedUser);
        $this->entityManager->flush();
        $this->entityManager->clear();

        $foundedUser = $repository->findOneById($user->id());
        $this->assertEquals(200, $foundedUser->walletAmount());
    }

    public function testShouldThrowExceptionWhenNotFoundUserById(): void
    {
        $this->expectException(UserNotFound::class);

        $mockPublisher = $this->getMockBuilder(DomainEventPublisher::class)
            ->disableOriginalConstructor()
            ->getMock();

        assert($mockPublisher instanceof DomainEventPublisher);

        $repository = new DoctrineUserRepository($this->entityManager, $mockPublisher);

        $repository->findOneById(1);
    }

    public function testShouldFindUserByEmail(): void
    {
        $user = CommonUser::create(
            'Leandro Eduardo Luan Costa',
            '122.004.920-49',
            'leandroeduardoluancosta-98@tirel.com.br',
            '8G4QM9qJOs',
            0
        );

        $this->entityManager->persist($user);
        $this->entityManager->flush();
        $this->entityManager->clear();

        $mockPublisher = $this->getMockBuilder(DomainEventPublisher::class)
            ->disableOriginalConstructor()
            ->getMock();

        assert($mockPublisher instanceof DomainEventPublisher);

        $repository = new DoctrineUserRepository($this->entityManager, $mockPublisher);

        $foundedUser = $repository->findOneOrNullByEmail('leandroeduardoluancosta-98@tirel.com.br');

        assert($foundedUser instanceof User);
        $this->assertNotNull($foundedUser);
    }

    public function testShouldReturnNullWhenNotFindUserByEmail(): void
    {
        $mockPublisher = $this->getMockBuilder(DomainEventPublisher::class)
            ->disableOriginalConstructor()
            ->getMock();

        assert($mockPublisher instanceof DomainEventPublisher);

        $repository = new DoctrineUserRepository($this->entityManager, $mockPublisher);

        $foundedUser = $repository->findOneOrNullByEmail('example@example.com');

        $this->assertNull($foundedUser);
    }

    public function testShouldFindUserByCpfOrCnpj(): void
    {
        $user = CommonUser::create(
            'Leandro Eduardo Luan Costa',
            '122.004.920-49',
            'leandroeduardoluancosta-98@tirel.com.br',
            '8G4QM9qJOs',
            0
        );

        $this->entityManager->persist($user);
        $this->entityManager->flush();
        $this->entityManager->clear();

        $mockPublisher = $this->getMockBuilder(DomainEventPublisher::class)
            ->disableOriginalConstructor()
            ->getMock();

        assert($mockPublisher instanceof DomainEventPublisher);

        $repository = new DoctrineUserRepository($this->entityManager, $mockPublisher);

        $foundedUser = $repository->findOneOrNullByCpfOrCnpj('122.004.920-49');

        assert($foundedUser instanceof User);
        $this->assertNotNull($foundedUser);
    }

    public function testShouldReturnNullWhenNotFindUserByCpfOrCnpj(): void
    {
        $mockPublisher = $this->getMockBuilder(DomainEventPublisher::class)
            ->disableOriginalConstructor()
            ->getMock();

        assert($mockPublisher instanceof DomainEventPublisher);

        $repository = new DoctrineUserRepository($this->entityManager, $mockPublisher);

        $foundedUser = $repository->findOneOrNullByEmail('75.778.772/0001-58');

        $this->assertNull($foundedUser);
    }

    /** @return array<mixed> */
    public function userProvider(): array
    {
        return [
            'Common User' => [
                CommonUser::create(
                    'Leandro Eduardo Luan Costa',
                    '122.004.920-49',
                    'leandroeduardoluancosta-98@tirel.com.br',
                    '8G4QM9qJOs',
                    0
                ),
            ],
            'Shopkeeper' => [
                Shopkeeper::create(
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
