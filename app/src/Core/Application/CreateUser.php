<?php

declare(strict_types=1);

namespace SimplePayment\Core\Application;

use Symfony\Component\Validator\Constraints as Assert;

/** @psalm-immutable */
class CreateUser
{
    /**
     * @Assert\Type("string")
     * @Assert\NotBlank
     */
    public string $fullName;

    /**
     * @Assert\Type("string")
     * @Assert\NotBlank
     */
    public string $cpfOrCnpj;

    /**
     * @Assert\Email()
     * @Assert\NotBlank
     */
    public string $email;

    /**
     * @Assert\Type("string")
     * @Assert\NotBlank
     */
    public string $password;

    /** @Assert\Type("float") */
    public float $amount;

    /** @Assert\Choice({"common", "shopkeeper"}) */
    public string $type;

    public function __construct(
        string $fullName,
        string $cpfOrCnpj,
        string $email,
        string $password,
        float $amount,
        string $type
    ) {
        $this->fullName = $fullName;
        $this->cpfOrCnpj = $cpfOrCnpj;
        $this->email = $email;
        $this->password = $password;
        $this->amount = $amount;
        $this->type = $type;
    }
}
