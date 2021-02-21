<?php

declare(strict_types=1);

namespace SimplePayment\Core\Infrastructure\Persistence;

use SimplePayment\Core\Domain\Exception\UserNotFound;
use SimplePayment\Core\Domain\User;
use SimplePayment\Core\Domain\UserRepository;

use function array_filter;
use function array_key_exists;
use function array_values;
use function count;
use function in_array;
use function reset;

class InMemoryUserRepository implements UserRepository
{
    /** @var User[] $items */
    private array $items;

    /** @param User[] $items */
    public function __construct(array $items)
    {
        $this->items = $items;
    }

    public function save(User $user): void
    {
        if (in_array($user, $this->items)) {
            return;
        }

        $this->items[] = $user;
    }

    public function findOneById(int $id): User
    {
        $key = $id - 1;

        if (! array_key_exists($key, $this->items)) {
            throw UserNotFound::withGivenId($id);
        }

        return $this->items[$key];
    }

    public function findOneOrNullByEmail(string $email): ?User
    {
        $filteredUsers = array_values(
            array_filter($this->items, static function (User $user) use ($email) {
                return $user->email() === $email;
            })
        );

        if (count($filteredUsers) === 0) {
            return null;
        }

        return reset($filteredUsers);
    }

    public function findOneOrNullByCpfOrCnpj(string $cpfOrCnpj): ?User
    {
        $filteredUsers = array_values(
            array_filter($this->items, static function (User $user) use ($cpfOrCnpj) {
                return $user->cpfOrCnpj() === $cpfOrCnpj;
            })
        );

        if (count($filteredUsers) === 0) {
            return null;
        }

        return reset($filteredUsers);
    }
}
