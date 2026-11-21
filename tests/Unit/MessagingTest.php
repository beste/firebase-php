<?php

declare(strict_types=1);

namespace Kreait\Firebase\Tests\Unit;

use Beste\Json;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Promise\Create;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\HttpFactory;
use GuzzleHttp\Psr7\Response;
use Kreait\Firebase\Exception\MessagingApiExceptionConverter;
use Kreait\Firebase\Messaging;
use Kreait\Firebase\Messaging\ApiClient;
use Kreait\Firebase\Messaging\AppInstanceApiClient;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Event\MessagesSent;
use Kreait\Firebase\Messaging\FirebaseInstallationIds;
use Kreait\Firebase\Messaging\RequestFactory;
use Kreait\Firebase\Tests\UnitTestCase;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\RequestInterface;

/**
 * @internal
 */
final class MessagingTest extends UnitTestCase
{
    public function testItDispatchesAnEventAfterSendingMessages(): void
    {
        $dispatcher = new class implements EventDispatcherInterface {
            public ?object $event = null;

            public function dispatch(object $event): object
            {
                return $this->event = $event;
            }
        };

        $client = new Client(['handler' => new MockHandler([
            new Response(200, [], '{"name":"message-id"}'),
        ])]);

        $exceptionConverter = new MessagingApiExceptionConverter();

        $messaging = new Messaging(
            new ApiClient($client, 'project-id', new RequestFactory(new HttpFactory(), new HttpFactory())),
            new AppInstanceApiClient($client, $exceptionConverter),
            $exceptionConverter,
            $dispatcher,
        );

        $report = $messaging->sendAll([CloudMessage::new()->withToken('token')], true);

        $this->assertInstanceOf(MessagesSent::class, $dispatcher->event);
        $this->assertSame($report, $dispatcher->event->report);
        $this->assertTrue($dispatcher->event->validateOnly);
    }

    public function testItSendsMulticastMessagesToFirebaseInstallationIds(): void
    {
        /** @var list<RequestInterface> $sentRequests */
        $sentRequests = [];

        $handler = static function (RequestInterface $request) use (&$sentRequests): PromiseInterface {
            $sentRequests[] = $request;

            return Create::promiseFor(new Response(200, [], '{"name":"message-id"}'));
        };

        $client = new Client(['handler' => $handler]);

        $exceptionConverter = new MessagingApiExceptionConverter();

        $messaging = new Messaging(
            new ApiClient($client, 'project-id', new RequestFactory(new HttpFactory(), new HttpFactory())),
            new AppInstanceApiClient($client, $exceptionConverter),
            $exceptionConverter,
        );

        $report = $messaging->sendMulticastToFids(
            CloudMessage::new()->withToken('token'),
            FirebaseInstallationIds::fromValue(['fid-1', 'fid-2']),
        );

        $this->assertSame(['fid-1', 'fid-2'], $report->validFids());
        $this->assertSame([], $report->validTokens());

        $sentTargets = [];

        foreach ($sentRequests as $sentRequest) {
            $payload = Json::decode((string) $sentRequest->getBody(), true);

            $this->assertArrayNotHasKey('token', $payload['message']);
            $sentTargets[] = $payload['message']['fid'];
        }

        $this->assertSame(['fid-1', 'fid-2'], $sentTargets);
    }

    public function testSendMulticastStillTargetsRegistrationTokens(): void
    {
        /** @var list<RequestInterface> $sentRequests */
        $sentRequests = [];

        $handler = static function (RequestInterface $request) use (&$sentRequests): PromiseInterface {
            $sentRequests[] = $request;

            return Create::promiseFor(new Response(200, [], '{"name":"message-id"}'));
        };

        $client = new Client(['handler' => $handler]);

        $exceptionConverter = new MessagingApiExceptionConverter();

        $messaging = new Messaging(
            new ApiClient($client, 'project-id', new RequestFactory(new HttpFactory(), new HttpFactory())),
            new AppInstanceApiClient($client, $exceptionConverter),
            $exceptionConverter,
        );

        $report = $messaging->sendMulticast(
            CloudMessage::new(),
            ['token-1', 'token-2'],
        );

        $this->assertSame(['token-1', 'token-2'], $report->validTokens());
        $this->assertSame([], $report->validFids());

        $sentTargets = [];

        foreach ($sentRequests as $sentRequest) {
            $payload = Json::decode((string) $sentRequest->getBody(), true);

            $this->assertArrayNotHasKey('fid', $payload['message']);
            $sentTargets[] = $payload['message']['token'];
        }

        $this->assertSame(['token-1', 'token-2'], $sentTargets);
    }

    public function testItValidatesFids(): void
    {
        /** @var list<RequestInterface> $sentRequests */
        $sentRequests = [];

        $handler = static function (RequestInterface $request) use (&$sentRequests): PromiseInterface {
            $sentRequests[] = $request;

            return Create::promiseFor(new Response(200, [], '{"name":"message-id"}'));
        };

        $client = new Client(['handler' => $handler]);

        $exceptionConverter = new MessagingApiExceptionConverter();

        $messaging = new Messaging(
            new ApiClient($client, 'project-id', new RequestFactory(new HttpFactory(), new HttpFactory())),
            new AppInstanceApiClient($client, $exceptionConverter),
            $exceptionConverter,
        );

        $result = $messaging->validateFids(['fid-1', 'fid-2']);

        $this->assertSame(['fid-1', 'fid-2'], $result['valid']);
        $this->assertSame([], $result['unknown']);
        $this->assertSame([], $result['invalid']);

        $this->assertCount(2, $sentRequests);

        foreach ($sentRequests as $sentRequest) {
            $payload = Json::decode((string) $sentRequest->getBody(), true);

            $this->assertTrue($payload['validate_only']);
            $this->assertArrayHasKey('fid', $payload['message']);
            $this->assertArrayNotHasKey('token', $payload['message']);
        }
    }
}
