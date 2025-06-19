<?php

declare(strict_types=1);

namespace Kreait\Firebase\Tests\Unit\Messaging;

use Beste\Json;
use Iterator;
use Kreait\Firebase\Messaging\ApnsConfig;
use Kreait\Firebase\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

/**
 * @internal
 */
final class ApnsConfigTest extends UnitTestCase
{
    #[Test]
    public function itIsEmptyWhenItIsEmpty(): void
    {
        $this->assertSame('[]', Json::encode(ApnsConfig::new()));
    }

    #[Test]
    public function itHasADefaultSound(): void
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

    #[Test]
    public function itHasABadge(): void
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
    #[Test]
    public function itCanBeCreatedFromAnArray(array $data): void
    {
        $this->assertJsonStringEqualsJsonString(
            Json::encode($data),
            Json::encode(ApnsConfig::fromArray($data)),
        );
    }

    public function itCanBeGivenData(): void
    {
        $config = ApnsConfig::fromArray(['data' => ['key' => 'value']]);

        $this->assertJsonStringEqualsJsonString(
            Json::encode($config),
            Json::encode(ApnsConfig::new()->withDataField('key', 'value')),
        );
    }

    #[Test]
    public function itCanHaveAnImmediatePriority(): void
    {
        $config = ApnsConfig::fromArray(['headers' => ['apns-priority' => '10']]);

        $this->assertJsonStringEqualsJsonString(
            Json::encode($config),
            Json::encode(ApnsConfig::new()->withImmediatePriority()),
        );
    }

    #[Test]
    public function itCanHaveAPowerConservingPriority(): void
    {
        $config = ApnsConfig::fromArray(['headers' => ['apns-priority' => '5']]);

        $this->assertJsonStringEqualsJsonString(
            Json::encode($config),
            Json::encode(ApnsConfig::new()->withPowerConservingPriority()),
        );
    }

    #[Test]
    public function itCanBeGivenALiveActivityTokenInsideAnArray(): void
    {
        $config = ApnsConfig::fromArray(['live_activity_token' => 'token']);

        $this->assertJsonStringEqualsJsonString(
            Json::encode($config),
            Json::encode(ApnsConfig::new()->withLiveActivityToken('token')),
        );
    }

    #[Test]
    public function itHasASubtitle(): void
    {
        $config = ApnsConfig::fromArray([
            'payload' => ['aps' => ['subtitle' => 'subtitle']],
        ]);

        $this->assertJsonStringEqualsJsonString(
            Json::encode($config),
            Json::encode(ApnsConfig::new()->withSubtitle('subtitle')),
        );
    }

    #[Test]
    public function itDoesApplyConditionableWhenConditionIsTrue(): void
    {
        $config = ApnsConfig::new()
            ->when(true, static fn(ApnsConfig $config): ApnsConfig => $config->withBadge(42));

        $this->assertJsonStringEqualsJsonString(
            Json::encode(['payload' => ['aps' => ['badge' => 42]]]),
            Json::encode($config),
        );

        $config = ApnsConfig::new()
            ->unless(false, static fn(ApnsConfig $config): ApnsConfig => $config->withBadge(42));

        $this->assertJsonStringEqualsJsonString(
            Json::encode(['payload' => ['aps' => ['badge' => 42]]]),
            Json::encode($config),
        );

        $config = ApnsConfig::new()
            ->when(true === false, static fn(ApnsConfig $config): ApnsConfig => $config->withBadge(42), fn(ApnsConfig $config): ApnsConfig => $config->withBadge(32));

        $this->assertJsonStringEqualsJsonString(
            Json::encode(['payload' => ['aps' => ['badge' => 32]]]),
            Json::encode($config),
        );
    }

    #[Test]
    public function itDoesNotApplyConditionableWhenConditionIsFalse(): void
    {
        $config = ApnsConfig::new()
            ->when(false, static fn(ApnsConfig $config): ApnsConfig => $config->withBadge(42));

        $this->assertJsonStringEqualsJsonString(
            Json::encode([]),
            Json::encode($config),
        );

        $config = ApnsConfig::new()
            ->unless(true, static fn(ApnsConfig $config): ApnsConfig => $config->withBadge(42));

        $this->assertJsonStringEqualsJsonString(
            Json::encode([]),
            Json::encode($config),
        );
    }

    public static function validDataProvider(): Iterator
    {
        yield 'full_config' => [[
            // https://firebase.google.com/docs/cloud-messaging/admin/send-messages#apns_specific_fields
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
