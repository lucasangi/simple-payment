<?php

declare(strict_types=1);

namespace SimplePayment\Core\Application;

use Symfony\Component\Validator\Constraints as Assert;

/** @psalm-immutable */
class FetchUserById
{
    /**
     * @Assert\Type("string")
     * @Assert\NotBlank
     */
    public string $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }
}
