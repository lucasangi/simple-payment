<?php

declare(strict_types=1);

namespace SimplePayment\Core\Infrastructure\Notifier;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\ResponseInterface;
use SimplePayment\Core\Domain\Exception\FailedToSendNotification;
use SimplePayment\Core\Domain\Notifier;

class MockNotifier implements Notifier
{
    private const WEBHOOK = 'https://run.mocky.io/v3/b19f7b9f-9cbf-4fc6-ad22-dc30601aec04';

    private Client $httpClient;

    public function __construct(Client $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function notify(): void
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
            throw FailedToSendNotification::fromConnectionError($connectionError);
        }
    }
}
