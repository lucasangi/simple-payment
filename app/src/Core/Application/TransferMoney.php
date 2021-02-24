<?php

declare(strict_types=1);

namespace SimplePayment\Core\Application;

use Symfony\Component\Validator\Constraints as Assert;

/** @psalm-immutable */
class TransferMoney
{
    /** @Assert\Type("float") */
    public float $value;

    /**
     * @Assert\Type("string")
     * @Assert\NotBlank
     */
    public string $payer;

    /**
     * @Assert\Type("string")
     * @Assert\NotBlank
     */
    public string $payee;

    public function __construct(float $value, string $payer, string $payee)
    {
        $this->value = $value;
        $this->payer = $payer;
        $this->payee = $payee;
    }
}
