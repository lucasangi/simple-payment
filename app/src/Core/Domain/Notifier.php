<?php

declare(strict_types=1);

namespace SimplePayment\Core\Domain;

interface Notifier
{
    public function notify(): void;
}
