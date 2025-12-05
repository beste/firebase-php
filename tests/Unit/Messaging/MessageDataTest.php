<?php

declare(strict_types=1);

namespace Kreait\Firebase\Tests\Unit\Messaging;

use InvalidArgumentException;
use Iterator;
use Kreait\Firebase\Messaging\MessageData;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\TestCase;

use function hex2bin;

/**
 * @internal
 */
final class MessageDataTest extends TestCase
{
    /**
     * @param array<non-empty-string, string> $data
     */
    #[DoesNotPerformAssertions]
    #[DataProvider('validData')]
    public function itAcceptsValidData(array $data): void
    {
        MessageData::fromArray($data);
    }

    /**
     * @param array<non-empty-string, string> $data
     */
    #[DataProvider('invalidData')]
    public function itRejectsInvalidData(array $data): void
    {
        $this->expectException(InvalidArgumentException::class);
        MessageData::fromArray($data);
    }

    /**
     * @see https://github.com/kreait/firebase-php/issues/709
     */
    public function testItDoesNotLowerCaseKeys(): void
    {
        $input = $output = ['notificationType' => 'email'];

        $data = MessageData::fromArray($input);

        $this->assertSame($data->toArray(), $output);
    }

    public static function validData(): Iterator
    {
        yield 'UTF-8 string' => [
            ['key' => 'Jérôme'],
        ];
    }

    public static function invalidData(): Iterator
    {
        // @see https://github.com/kreait/firebase-php/issues/441
        yield 'binary data' => [
            ['key' => hex2bin('81612bcffb')], // generated with \openssl_random_pseudo_bytes(5)
        ];
        yield 'reserved_key_from' => [
            ['from' => 'any'],
        ];
        // According to the docs, "notification" is reserved, but it's still accepted ¯\_(ツ)_/¯
        /*
        'reserved_key_notification' => [
            ['notification' => 'any'],
        ],
        */
        yield 'reserved_key_message_type' => [
            ['message_type' => 'any'],
        ];
        yield 'reserved_key_prefix_google' => [
            ['google_is_reserved' => 'any'],
        ];
        yield 'reserved_key_prefix_gcm' => [
            ['gcm_is_reserved' => 'any'],
        ];
    }
}
