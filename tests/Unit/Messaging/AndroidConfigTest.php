<?php

declare(strict_types=1);

namespace Kreait\Firebase\Tests\Unit\Messaging;

use Beste\Json;
use Iterator;
use Kreait\Firebase\Exception\Messaging\InvalidArgument;
use Kreait\Firebase\Messaging\AndroidConfig;
use Kreait\Firebase\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;

/**
 * @internal
 *
 * @phpstan-import-type AndroidConfigShape from AndroidConfig
 */
final class AndroidConfigTest extends UnitTestCase
{
    public function testItIsEmptyWhenItIsEmpty(): void
    {
        $this->assertSame('[]', Json::encode(AndroidConfig::new()));
    }

    public function testItHasADefaultSound(): void
    {
        $expected = [
            'notification' => [
                'sound' => 'default',
            ],
        ];

        $this->assertJsonStringEqualsJsonString(
            Json::encode($expected),
            Json::encode(AndroidConfig::new()->withDefaultSound()),
        );
    }

    public function testItCanHaveAPriority(): void
    {
        $config = AndroidConfig::new()->withNormalMessagePriority();
        $this->assertSame('normal', $config->jsonSerialize()['priority']);

        $config = AndroidConfig::new()->withHighMessagePriority();
        $this->assertSame('high', $config->jsonSerialize()['priority']);
    }

    /**
     * @param AndroidConfigShape $data
     */
    #[DataProvider('validDataProvider')]
    public function testItCanBeCreatedFromAnArray(array $data): void
    {
        $config = AndroidConfig::fromArray($data);

        $this->assertEqualsCanonicalizing($data, $config->jsonSerialize());
    }

    #[DoesNotPerformAssertions]
    #[DataProvider('validTtlValues')]
    public function testItAcceptsValidTTLs(int|string|null $ttl): void
    {
        AndroidConfig::fromArray([
            'ttl' => $ttl,
        ]);
    }

    #[DataProvider('invalidTtlValues')]
    public function testItRejectsInvalidTTLs(int|string $ttl): void
    {
        $this->expectException(InvalidArgument::class);

        AndroidConfig::fromArray([
            'ttl' => $ttl,
        ]);
    }

    public static function validDataProvider(): Iterator
    {
        yield 'full_config' => [[
            'ttl' => '3600s',
            'priority' => 'normal',
            'notification' => [
                'title' => '$GOOGLE up 1.43% on the day',
                'body' => '$GOOGLE gained 11.80 points to close at 835.67, up 1.43% on the day.',
                'icon' => 'stock_ticker_update',
                'color' => '#f45342',
                'sound' => 'default',
            ],
        ]];
    }

    public static function validTtlValues(): Iterator
    {
        yield 'positive int' => [1];
        yield 'positive numeric string' => ['1'];
        yield 'expected string' => ['1s'];
        yield 'zero' => [0];
        yield 'zero string' => ['0'];
        yield 'zero string with suffix' => ['0s'];
        yield 'null (#719)' => [null];
    }

    public static function invalidTtlValues(): Iterator
    {
        yield 'wrong suffix' => ['1m'];
        yield 'negative int' => [-1];
        yield 'negative string' => ['-1'];
        yield 'negative string with suffix' => ['-1s'];
    }
}
