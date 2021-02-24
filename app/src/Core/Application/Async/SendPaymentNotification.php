<?php

declare(strict_types=1);

namespace SimplePayment\Core\Application\Async;

use Symfony\Component\Validator\Constraints as Assert;

/** @psalm-immutable */
class SendPaymentNotification
{
    /**
     * @Assert\Type("string")
     * @Assert\NotBlank
     */
    public string $userId;

    /** @Assert\Type("float") */
    public float $amount;

    public function __construct(string $userId, float $amount)
    {
        $this->userId = $userId;
        $this->amount = $amount;
    }
}
