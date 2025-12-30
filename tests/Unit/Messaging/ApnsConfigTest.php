<?php

declare(strict_types=1);

namespace Kreait\Firebase\Tests\Unit\Messaging;

use Beste\Json;
use Iterator;
use Kreait\Firebase\Messaging\ApnsConfig;
use Kreait\Firebase\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * @internal
 */
final class ApnsConfigTest extends UnitTestCase
{
    public function testItIsEmptyWhenItIsEmpty(): void
    {
        $this->assertSame('[]', Json::encode(ApnsConfig::new()));
    }

    public function testItHasADefaultSound(): void
    {
        $config = ApnsConfig::fromArray([
            'payload' => [
                'aps' => [
                    'sound' => 'default',
                ],
            ],
        ]);

        $this->assertJsonStringEqualsJsonString(
            Json::encode($config),
            Json::encode(ApnsConfig::new()->withDefaultSound()),
        );
    }

    public function testItHasABadge(): void
    {
        $config = ApnsConfig::fromArray([
            'payload' => [
                'aps' => [
                    'badge' => 123,
                ],
            ],
        ]);

        $this->assertJsonStringEqualsJsonString(
            Json::encode($config),
            Json::encode(ApnsConfig::new()->withBadge(123)),
        );
    }

    /**
     * @param array<string, mixed> $data
     */
    #[DataProvider('validDataProvider')]
    public function testItCanBeCreatedFromAnArray(array $data): void
    {
        $this->assertJsonStringEqualsJsonString(
            Json::encode($data),
            Json::encode(ApnsConfig::fromArray($data)),
        );
    }

    public function testItCanBeGivenData(): void
    {
        $config = ApnsConfig::fromArray(['payload' => ['key' => 'value']]);

        $this->assertJsonStringEqualsJsonString(
            Json::encode($config),
            Json::encode(ApnsConfig::new()->withDataField('key', 'value')),
        );
    }

    public function testItCanHaveAnImmediatePriority(): void
    {
        $config = ApnsConfig::fromArray(['headers' => ['apns-priority' => '10']]);

        $this->assertJsonStringEqualsJsonString(
            Json::encode($config),
            Json::encode(ApnsConfig::new()->withImmediatePriority()),
        );
    }

    public function testItCanHaveAPowerConservingPriority(): void
    {
        $config = ApnsConfig::fromArray(['headers' => ['apns-priority' => '5']]);

        $this->assertJsonStringEqualsJsonString(
            Json::encode($config),
            Json::encode(ApnsConfig::new()->withPowerConservingPriority()),
        );
    }

    public function testItCanBeGivenALiveActivityTokenInsideAnArray(): void
    {
        $config = ApnsConfig::fromArray(['live_activity_token' => 'token']);

        $this->assertJsonStringEqualsJsonString(
            Json::encode($config),
            Json::encode(ApnsConfig::new()->withLiveActivityToken('token')),
        );
    }

    public function testItHasASubtitle(): void
    {
        $config = ApnsConfig::fromArray([
            'payload' => ['aps' => ['subtitle' => 'subtitle']],
        ]);

        $this->assertJsonStringEqualsJsonString(
            Json::encode($config),
            Json::encode(ApnsConfig::new()->withSubtitle('subtitle')),
        );
    }

    /**
     * @return Iterator<array<array<int, array<string, mixed>>, mixed>>
     */
    public static function validDataProvider(): Iterator
    {
        yield 'full_config' => [[
            'headers' => [
                'apns-priority' => '10',
            ],
            'payload' => [
                'aps' => [
                    'alert' => [
                        'title' => '$GOOGLE up 1.43% on the day',
                        'body' => '$GOOGLE gained 11.80 points to close at 835.67, up 1.43% on the day.',
                    ],
                    'badge' => 42,
                    'sound' => 'default',
                ],
            ],
            'live_activity_token' => 'token',
        ]];
    }
}
