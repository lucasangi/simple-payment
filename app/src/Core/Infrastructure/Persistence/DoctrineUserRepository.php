<?php

declare(strict_types=1);

namespace SimplePayment\Core\Infrastructure\Persistence;

use Doctrine\Persistence\ObjectManager;
use SimplePayment\Core\Domain\Exception\UserNotFound;
use SimplePayment\Core\Domain\User;
use SimplePayment\Core\Domain\UserRepository;
use SimplePayment\Framework\DomainEvent\Domain\DomainEventPublisher;
use SimplePayment\Framework\Id\Domain\Id;

class DoctrineUserRepository implements UserRepository
{
    private const ENTITY = User::class;

    private ObjectManager $objectManager;
    private DomainEventPublisher $publisher;

    public function __construct(ObjectManager $objectManager, DomainEventPublisher $publisher)
    {
        $this->objectManager = $objectManager;
        $this->publisher = $publisher;
    }

    public function save(User $user): void
    {
        $this->objectManager->persist($user);

        foreach ($user->domainEvents() as $event) {
            $this->publisher->publish($event);
        }
    }

    public function findOneById(Id $id): User
    {
        $user = $this->objectManager->getRepository(self::ENTITY)->find($id);

        if ($user instanceof User) {
            return $user;
        }

        throw UserNotFound::withGivenId($id);
    }

    public function findOneOrNullByEmail(string $email): ?User
    {
        $user = $this->objectManager->getRepository(self::ENTITY)
            ->findOneBy(['email' => $email]);

        if ($user instanceof User) {
            return $user;
        }

        return null;
    }

    public function findOneOrNullByCpfOrCnpj(string $cpfOrCnpj): ?User
    {
        $user = $this->objectManager->getRepository(self::ENTITY)
            ->findOneBy(['cpfOrCnpj' => $cpfOrCnpj]);

        if ($user instanceof User) {
            return $user;
        }

        return null;
    }
}
