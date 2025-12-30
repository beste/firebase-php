<?php

declare(strict_types=1);

namespace Kreait\Firebase\Tests\Unit\Messaging;

use Iterator;
use Kreait\Firebase\Messaging\RegistrationToken;
use Kreait\Firebase\Messaging\RegistrationTokens;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
final class RegistrationTokensTest extends TestCase
{
    #[DataProvider('validValuesWithExpectedCounts')]
    public function testItCanBeCreatedFromValues(int $expectedCount, mixed $value): void
    {
        $tokens = RegistrationTokens::fromValue($value);

        $this->assertCount($expectedCount, $tokens);
    }

    public function testItReturnsStrings(): void
    {
        $token = RegistrationToken::fromValue('foo');

        $tokens = RegistrationTokens::fromValue([$token, $token]);
        $this->assertEqualsCanonicalizing(['foo', 'foo'], $tokens->asStrings());
    }

    /**
     * @return Iterator<array<array<int, mixed>, mixed>>
     */
    public static function validValuesWithExpectedCounts(): Iterator
    {
        $foo = RegistrationToken::fromValue('foo');
        yield 'string' => [1, 'foo'];
        yield 'token object' => [1, $foo];
        yield 'collection' => [2, new RegistrationTokens($foo, $foo)];
        yield 'array with mixed values' => [2, [$foo, 'bar']];
    }
}
