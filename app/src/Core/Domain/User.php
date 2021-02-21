<?php

declare(strict_types=1);

namespace SimplePayment\Core\Domain;

use Doctrine\ORM\Mapping as ORM;
use SimplePayment\Core\Domain\Event\PaymentReceived;
use SimplePayment\Framework\DomainEvent\Domain\DomainEvent;

/**
 * @ORM\Entity()
 * @ORM\Table(
 *   name="users",
 *   uniqueConstraints={
 *     @ORM\UniqueConstraint(name="user_email",columns={"email"}),
 *     @ORM\UniqueConstraint(name="user_cpf_cpnj",columns={"cpf_or_cnpj"})
 *   },
 * )
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({
 *   "common" = "SimplePayment\Core\Domain\CommonUser",
 *   "shopkeeper" = "SimplePayment\Core\Domain\Shopkeeper"
 * })
 */
abstract class User
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private int $id;

    /** @ORM\Column(type="string") */
    protected string $fullName;

    /** @ORM\Column(type="string", name="cpf_or_cnpj") */
    protected string $cpfOrCnpj;

    /** @ORM\Column(type="string") */
    protected string $email;

    /** @ORM\Column(type="string") */
    protected string $password;

    /** @ORM\Embedded(class="Wallet") */
    protected Wallet $wallet;

    /** @var DomainEvent[] $domainEvents */
    protected array $domainEvents;

    protected function __construct(
        string $fullName,
        string $cpfOrCnpj,
        string $email,
        string $password,
        float $amount
    ) {
        $this->fullName = $fullName;
        $this->cpfOrCnpj = $cpfOrCnpj;
        $this->email = $email;
        $this->password = $password;
        $this->wallet = Wallet::create($amount);
    }

    public function id(): int
    {
        return $this->id;
    }

     /**
      * @return DomainEvent[]
      */
    public function domainEvents(): array
    {
        if (empty($this->domainEvents)) {
            return [];
        }

        $storedDomainEvents = $this->domainEvents;
        $this->domainEvents = [];

        return $storedDomainEvents;
    }

    public function fullName(): string
    {
        return $this->fullName;
    }

    public function cpfOrCnpj(): string
    {
        return $this->cpfOrCnpj;
    }

    public function email(): string
    {
        return $this->email;
    }

    public function password(): string
    {
        return $this->password;
    }

    public function walletAmount(): float
    {
        return $this->wallet->amount();
    }

    public function receive(float $amount): void
    {
        $this->wallet->deposit($amount);

        $this->domainEvents[] = PaymentReceived::create($amount);
    }
}
