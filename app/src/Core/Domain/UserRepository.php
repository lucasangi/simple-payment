<?php

declare(strict_types=1);

namespace SimplePayment\Core\Domain;

use SimplePayment\Core\Domain\Exception\UserNotFound;

interface UserRepository
{
    public function save(User $user): void;

    /** @throws UserNotFound */
    public function findOneById(int $id): User;

    public function findOneOrNullByEmail(string $email): ?User;

    public function findOneOrNullByCpfOrCnpj(string $cpfOrCnpj): ?User;
}
