<?php

declare(strict_types=1);

namespace SimplePayment\Core\Infrastructure\Authentication;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\ResponseInterface;
use SimplePayment\Core\Domain\Authenticator;
use SimplePayment\Core\Domain\Exception\AuthenticationFailed;

class MockAuthenticator implements Authenticator
{
    private const WEBHOOK = 'https://run.mocky.io/v3/8fafdd68-a090-496f-8c9a-3442cf30dae6';

    private Client $httpClient;

    public function __construct(Client $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function authenticate(): void
    {
        $this->sendRequest($this->buildRequest());
    }

    private function buildRequest(): Request
    {
        return new Request(
            'GET',
            self::WEBHOOK,
            ['Content-Type' => 'application/json']
        );
    }

    private function sendRequest(Request $request): ResponseInterface
    {
        try {
            return $this->httpClient->send($request);
        } catch (RequestException $connectionError) {
            throw AuthenticationFailed::fromAuthenticationError($connectionError);
        }
    }
}
