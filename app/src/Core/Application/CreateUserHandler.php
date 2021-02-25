<?php

declare(strict_types=1);

namespace SimplePayment\Core\Application;

use SimplePayment\Core\Application\Exception\InvalidUser;
use SimplePayment\Core\Domain\CommonUser;
use SimplePayment\Core\Domain\Shopkeeper;
use SimplePayment\Core\Domain\User;
use SimplePayment\Core\Domain\UserRepository;
use SimplePayment\Framework\Id\Domain\Id;
use SimplePayment\Framework\PasswordEncoder\Domain\PasswordEncoder;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class CreateUserHandler implements MessageHandlerInterface
{
    private UserRepository $repository;
    private PasswordEncoder $passwordEncoder;

    public function __construct(UserRepository $repository, PasswordEncoder $passwordEncoder)
    {
        $this->repository = $repository;
        $this->passwordEncoder = $passwordEncoder;
    }

    public function __invoke(CreateUser $command): Id
    {
        if ($command->type === 'common') {
            return $this->createCommonUser($command);
        }

        if ($command->type === 'shopkeeper') {
            return $this->createShopkeeper($command);
        }

        throw InvalidUser::fromInvalidUserType($command->type);
    }

    private function createCommonUser(CreateUser $command): Id
    {
        $id = Id::generate();
        $fullName = $command->fullName;
        $cpf = $command->cpfOrCnpj;
        $email = $command->email;
        $password = $this->passwordEncoder->encodePassword($command->password);
        $amount = $command->amount;

        $this->throwExceptionIfEmailAlreadyExists($email);
        $this->throwExceptionIfCPForCPNJAlreadyExists($cpf);

        $commonUser = CommonUser::create(
            $id,
            $fullName,
            $cpf,
            $email,
            $password,
            $amount
        );

        $this->repository->save($commonUser);

        return $id;
    }

    private function createShopkeeper(CreateUser $command): Id
    {
        $id = Id::generate();
        $fullName = $command->fullName;
        $cnpj = $command->cpfOrCnpj;
        $email = $command->email;
        $password = $command->password;
        $amount = $command->amount;

        $this->throwExceptionIfEmailAlreadyExists($email);
        $this->throwExceptionIfCPForCPNJAlreadyExists($cnpj);

        $shopkeeper = Shopkeeper::create(
            $id,
            $fullName,
            $cnpj,
            $email,
            $password,
            $amount
        );

        $this->repository->save($shopkeeper);

        return $id;
    }

    private function throwExceptionIfEmailAlreadyExists(string $email): void
    {
        $foundedUser = $this->repository->findOneOrNullByEmail($email);

        if ($foundedUser instanceof User) {
            throw InvalidUser::fromExistingEmail($email);
        }
    }

    private function throwExceptionIfCPForCPNJAlreadyExists(string $cprOrCnpj): void
    {
        $foundedUser = $this->repository->findOneOrNullByCpfOrCnpj($cprOrCnpj);

        if ($foundedUser instanceof User) {
            throw InvalidUser::fromExistingCPForCPNJ($cprOrCnpj);
        }
    }
}
