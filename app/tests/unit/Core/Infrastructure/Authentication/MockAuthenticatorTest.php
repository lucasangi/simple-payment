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
use SimplePayment\Core\Domain\Exception\AuthenticationFailed;
use SimplePayment\Core\Infrastructure\Authentication\MockAuthenticator;

class MockAuthenticatorTest extends TestCase
{
    private string $mockURL = 'https://run.mocky.io/v3/8fafdd68-a090-496f-8c9a-3442cf30dae6';

    public function testShouldAuthenticateUser(): void
    {
        $history = [];
        $response = new Response(
            200,
            ['Content-Type' => 'application/json'],
            '{"message":"Autorizado"}'
        );

        $httpClient = $this->httpClient($history, $response);

        $authenticator = new MockAuthenticator($httpClient);

        $authenticator->authenticate();

        $this->assertCount(1, $history);

        [$occurrence] = $history;

        $request = $occurrence['request'];

        $this->assertEquals($this->mockURL, (string) $request->getUri());
    }

    public function testShouldThrowExceptionWhenConnectionErrorWasOccurred(): void
    {
        $this->expectException(AuthenticationFailed::class);

        $history = [];
        $exceptionTitle = 'Error Communicating with Server';
        $exceptionRequest = new Request('GET', $this->mockURL);
        $exception = new RequestException($exceptionTitle, $exceptionRequest);

        $httpClient = $this->httpClient($history, $exception);

        $authenticator = new MockAuthenticator($httpClient);

        $authenticator->authenticate();
    }

    public function testShouldThrowExceptionWhenAuthenticationFailed(): void
    {
        $this->expectException(AuthenticationFailed::class);

        $history = [];
        $response = new Response(
            401,
            ['Content-Type' => 'application/json'],
            '{"message":"Autenticação Falhou"}'
        );

        $httpClient = $this->httpClient($history, $response);

        $authenticator = new MockAuthenticator($httpClient);

        $authenticator->authenticate();
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
