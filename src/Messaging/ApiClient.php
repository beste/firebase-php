<?php

declare(strict_types=1);

namespace Kreait\Firebase\Messaging;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Pool;
use GuzzleHttp\Promise\PromiseInterface;
use Iterator;
use Psr\Http\Message\RequestInterface;

/**
 * @internal
 */
final readonly class ApiClient
{
    public function __construct(
        private ClientInterface $client,
        private string $projectId,
        private RequestFactory $requestFactory,
    ) {
    }

    public function createSendRequestForMessage(Message $message, bool $validateOnly): RequestInterface
    {
        return $this->requestFactory->createRequest($message, $this->projectId, $validateOnly);
    }

    /**
     * @param list<RequestInterface>|Iterator<RequestInterface> $requests
     * @param array<string, mixed> $config
     */
    public function pool(array|Iterator $requests, array $config): PromiseInterface
    {
        $pool = new Pool($this->client, $requests, $config);

        return $pool->promise();
    }
}
