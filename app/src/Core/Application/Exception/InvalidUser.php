<?php

declare(strict_types=1);

namespace SimplePayment\Core\Application\Exception;

use InvalidArgumentException;
use Lcobucci\ErrorHandling\Problem\Conflict;
use Lcobucci\ErrorHandling\Problem\Titled;

use function sprintf;

class InvalidUser extends InvalidArgumentException implements Conflict, Titled
{
    public static function fromExistingEmail(string $email): self
    {
        return new self(
            sprintf('The given email (%s) already exists.', $email)
        );
    }

    public static function fromExistingCPForCPNJ(string $cpfOrCnpj): self
    {
        return new self(
            sprintf('The given CPF/CPNJ (%s) already exists.', $cpfOrCnpj)
        );
    }

    public static function fromInvalidUserType(string $type): self
    {
        return new self(
            sprintf('The given user type (%s) not exists.', $type)
        );
    }

    public function getTitle(): string
    {
        return 'Invalid User.';
    }
}
