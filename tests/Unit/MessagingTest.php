<?php

declare(strict_types=1);

namespace Kreait\Firebase\Tests\Unit;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\HttpFactory;
use GuzzleHttp\Psr7\Response;
use Kreait\Firebase\Exception\MessagingApiExceptionConverter;
use Kreait\Firebase\Messaging;
use Kreait\Firebase\Messaging\ApiClient;
use Kreait\Firebase\Messaging\AppInstanceApiClient;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Event\MessagesSent;
use Kreait\Firebase\Messaging\RequestFactory;
use Kreait\Firebase\Tests\UnitTestCase;
use Psr\EventDispatcher\EventDispatcherInterface;

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
}
