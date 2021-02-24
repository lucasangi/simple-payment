<?php

declare(strict_types=1);

namespace SimplePayment\Core\Domain;

use SimplePayment\Core\Domain\Exception\AuthenticationFailed;

interface Authenticator
{
    /** @throws AuthenticationFailed */
    public function authenticate(): void;
}
