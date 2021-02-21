<?php

declare(strict_types=1);

namespace SimplePayment\Framework\Exception\Domain;

use InvalidArgumentException;

use function array_reverse;
use function assert;
use function count;

abstract class ErrorListener
{
    /** @var string[] $errorHandlers */
    private array $errorHandlers;

    /** @param string[] $errorHandlers */
    public function __construct(array $errorHandlers)
    {
        if (count($errorHandlers) === 0) {
            throw new InvalidArgumentException(
                'It is necessary to have at least one registered error handler.'
            );
        }

        $this->errorHandlers = $errorHandlers;
    }

    public function constructErrorHandlerChain(): ErrorHandler
    {
        $chain = null;

        assert(count($this->errorHandlers) > 0);

        foreach (array_reverse($this->errorHandlers) as $handler) {
            $chain = new $handler($chain);
            assert($chain instanceof ErrorHandler);
        }

        return $chain;
    }
}
