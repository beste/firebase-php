<?php

declare(strict_types=1);

namespace Kreait\Firebase\Tests\Unit;

use Beste\Json;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use Kreait\Firebase\Exception\RemoteConfigApiExceptionConverter;
use Kreait\Firebase\Http\ErrorResponseParser;
use Kreait\Firebase\RemoteConfig;
use Kreait\Firebase\RemoteConfig\ApiClient;
use Kreait\Firebase\RemoteConfig\Event\TemplatePublished;
use Kreait\Firebase\RemoteConfig\Event\TemplateRolledBack;
use Kreait\Firebase\RemoteConfig\Template;
use Kreait\Firebase\RemoteConfig\VersionNumber;
use Kreait\Firebase\Tests\UnitTestCase;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * @internal
 */
final class RemoteConfigTest extends UnitTestCase
{
    public function testItDispatchesEventsAfterPublishingAndRollingBackTemplates(): void
    {
        $dispatcher = new class implements EventDispatcherInterface {
            /** @var list<object> */
            public array $events = [];

            public function dispatch(object $event): object
            {
                $this->events[] = $event;

                return $event;
            }
        };

        $client = new Client(['handler' => new MockHandler([
            new Response(200, ['ETag' => 'published-etag'], $this->templateResponse('2')),
            new Response(200, ['ETag' => 'rollback-etag'], $this->templateResponse('3', '1')),
        ])]);

        $remoteConfig = new RemoteConfig(
            new ApiClient('project-id', $client, new RemoteConfigApiExceptionConverter(new ErrorResponseParser())),
            $dispatcher,
        );

        $template = Template::new();
        $etag = $remoteConfig->publish($template);
        $versionNumber = VersionNumber::fromValue(1);
        $rolledBackTemplate = $remoteConfig->rollbackToVersion($versionNumber);

        $published = $dispatcher->events[0];
        $this->assertInstanceOf(TemplatePublished::class, $published);
        $this->assertSame('2', (string) $published->publishedTemplate->version()?->versionNumber());
        $this->assertSame($etag, $published->etag);

        $rolledBack = $dispatcher->events[1];
        $this->assertInstanceOf(TemplateRolledBack::class, $rolledBack);
        $this->assertSame($versionNumber, $rolledBack->rollbackTargetVersionNumber);
        $this->assertSame($rolledBackTemplate, $rolledBack->activeTemplate);
        $this->assertSame('3', (string) $rolledBack->activeTemplate->version()?->versionNumber());
    }

    private function templateResponse(string $versionNumber, ?string $rollbackSource = null): string
    {
        $version = [
            'versionNumber' => $versionNumber,
            'updateTime' => '2026-01-01T00:00:00Z',
            'updateUser' => [],
            'updateOrigin' => 'REST_API',
            'updateType' => $rollbackSource === null ? 'INCREMENTAL_UPDATE' : 'ROLLBACK',
        ];

        if ($rollbackSource !== null) {
            $version['rollbackSource'] = $rollbackSource;
        }

        return Json::encode(['version' => $version]);
    }
}
