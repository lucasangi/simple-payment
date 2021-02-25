<?php

declare(strict_types=1);

namespace SimplePayment\Core\Application;

use SimplePayment\Core\Domain\User;
use SimplePayment\Core\Domain\UserRepository;
use SimplePayment\Framework\Id\Domain\Id;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class FetchUserByIdFinder implements MessageHandlerInterface
{
    private UserRepository $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    /** @return array<string, string|float> */
    public function __invoke(FetchUserById $command): array
    {
        $user = $this->repository->findOneById(Id::fromString($command->id));

        return $this->userDataToArrayTransformer($user);
    }

    /** @return array<string, string|float> */
    private function userDataToArrayTransformer(User $user): array
    {
        return [
            'id' => $user->id()->toString(),
            'full_name' => $user->fullName(),
            'email' => $user->email(),
            'cpf_cnpj' => $user->cpfOrCnpj(),
            'wallet_amount' => $user->walletAmount(),
            'type' => $user->type(),
        ];
    }
}
