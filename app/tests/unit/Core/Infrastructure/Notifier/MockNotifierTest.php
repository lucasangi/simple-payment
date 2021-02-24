<?php

declare(strict_types=1);

namespace SimplePayment\Tests\Core\Infrastructure\Authentication;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use SimplePayment\Core\Domain\Exception\FailedToSendNotification;
use SimplePayment\Core\Infrastructure\Notifier\MockNotifier;

class MockNotifierTest extends TestCase
{
    private string $mockURL = 'https://run.mocky.io/v3/b19f7b9f-9cbf-4fc6-ad22-dc30601aec04';

    public function testShouldSendNotificationToUser(): void
    {
        $history = [];
        $response = new Response(
            200,
            ['Content-Type' => 'application/json'],
            '{"message":"Autorizado"}'
        );

        $httpClient = $this->httpClient($history, $response);

        $notifier = new MockNotifier($httpClient);

        $notifier->notify();

        $this->assertCount(1, $history);

        [$occurrence] = $history;

        $request = $occurrence['request'];

        $this->assertEquals($this->mockURL, (string) $request->getUri());
    }

    public function testShouldThrowExceptionWhenConnectionErrorWasOccurred(): void
    {
        $this->expectException(FailedToSendNotification::class);

        $history = [];
        $exceptionTitle = 'Error Communicating with Server';
        $exceptionRequest = new Request('GET', $this->mockURL);
        $exception = new RequestException($exceptionTitle, $exceptionRequest);

        $httpClient = $this->httpClient($history, $exception);

        $notifier = new MockNotifier($httpClient);

        $notifier->notify();
    }

    /**
     * @param array<mixed>                       $container
     * @param RequestException|ResponseInterface $response
     */
    private function httpClient(array &$container, $response): Client
    {
        $history = Middleware::history($container);
        $mock = new MockHandler([$response]);
        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push($history);

        return new Client(['handler' => $handlerStack]);
    }
}
