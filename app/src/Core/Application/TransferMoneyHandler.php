<?php

declare(strict_types=1);

namespace SimplePayment\Core\Application;

use SimplePayment\Core\Application\Exception\InvalidTransaction;
use SimplePayment\Core\Domain\User;
use SimplePayment\Core\Domain\UserRepository;
use SimplePayment\Core\Domain\UserWithTransactionOption;
use SimplePayment\Framework\Id\Domain\Id;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

use function assert;

class TransferMoneyHandler implements MessageHandlerInterface
{
    private UserRepository $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(TransferMoney $command): void
    {
        $payer = $this->repository->findOneById(Id::fromString($command->payer));
        $payee = $this->repository->findOneById(Id::fromString($command->payee));

        $this->throwExceptionIfUserCanNotTransferMoney($payer);

        assert($payer instanceof UserWithTransactionOption);

        $payer->transferAmountToUser($command->value, $payee);

        $this->repository->save($payee);
        $this->repository->save($payer);
    }

    /**
     * @throws InvalidTransaction
     */
    private function throwExceptionIfUserCanNotTransferMoney(User $user): void
    {
        if ($user instanceof UserWithTransactionOption) {
            return;
        }

        throw InvalidTransaction::fromShopkeeperAsPayer();
    }
}
