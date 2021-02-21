<?php

declare(strict_types=1);

namespace SimplePayment\Framework\Exception\Infrastructure\Listener;

use SimplePayment\Framework\Exception\Domain\ErrorListener;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class SymfonyExceptionListener extends ErrorListener
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $errorHandlerChain = $this->constructErrorHandlerChain();

        $response = $errorHandlerChain->handle($exception);

        $event->setResponse($response);
    }
}
