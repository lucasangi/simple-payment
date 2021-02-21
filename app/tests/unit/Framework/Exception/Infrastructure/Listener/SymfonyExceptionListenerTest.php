<?php

declare(strict_types=1);

namespace SimplePayment\Framework\Exception\Infrastructure\Listener;

use Exception;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use SimplePayment\Framework\Exception\Infrastructure\Handler\DebugErrorHandler;
use SimplePayment\Framework\Exception\Infrastructure\Handler\GenericErrorHandler;
use SimplePayment\Framework\Exception\Infrastructure\Handler\LcobucciErrorHandler;
use SimplePayment\Framework\Exception\Infrastructure\Handler\NotFoundHttpErrorHandler;
use SimplePayment\Framework\Exception\Infrastructure\Handler\ValidationFailedErrorHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Throwable;

use function assert;

class SymfonyExceptionListenerTest extends TestCase
{
    /** @var string[] $errorHandlers */
    private array $errorHandlers = [
        NotFoundHttpErrorHandler::class,
        ValidationFailedErrorHandler::class,
        LcobucciErrorHandler::class,
        DebugErrorHandler::class,
        GenericErrorHandler::class,
    ];

    /** @dataProvider exceptionProvider */
    public function testHandleException(Throwable $exception): void
    {
        $listener = new SymfonyExceptionListener($this->errorHandlers);
        $event = $this->aExceptionEventFor($exception);

        $listener->onKernelException($event);

        $this->assertInstanceOf(Response::class, $event->getResponse());
    }

    /** @return array<mixed> */
    public function exceptionProvider(): array
    {
        return [
            'When is an Exception' => [new Exception()],
            'When is a RuntimeException' => [new RuntimeException()],
            'When is a InvalidArgumentException' => [new InvalidArgumentException()],
            'When is a NotFoundHttpException' => [new NotFoundHttpException('No route found for \"GET /example')],
        ];
    }

    private function aExceptionEventFor(Throwable $throwable): ExceptionEvent
    {
        $kernel = $this->getMockBuilder(HttpKernelInterface::class)->getMock();
        $request = Request::create('/');

        assert($kernel instanceof HttpKernelInterface);

        return new ExceptionEvent($kernel, $request, HttpKernelInterface::MASTER_REQUEST, $throwable);
    }
}
