<?php

declare(strict_types=1);

namespace Kreait\Firebase\Tests\Unit\Messaging;

use Iterator;
use Kreait\Firebase\Exception\Messaging\InvalidArgument;
use Kreait\Firebase\Messaging\Condition;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
final class ConditionTest extends TestCase
{
    #[DataProvider('valueProvider')]
    public function testFromValue(string $expected, string $value): void
    {
        $this->assertSame($expected, Condition::fromValue($value)->value());
    }

    #[DataProvider('invalidValueProvider')]
    public function testFromInvalidValue(string $value): void
    {
        $this->expectException(InvalidArgument::class);
        Condition::fromValue($value);
    }

    public function testNoMoreThanFiveTopics(): void
    {
        $valid = "'a' in topics && 'b' in topics || 'c' in topics || 'd' in topics || 'e' in topics";
        $invalid = $valid." || 'f' in topics";

        // This should not throw an exception
        Condition::fromValue($valid);

        $this->expectException(InvalidArgument::class);
        Condition::fromValue($invalid);
    }

    public static function valueProvider(): Iterator
    {
        yield 'single quotes' => ["'dogs' in topics || 'cats' in topics", "'dogs' in topics || 'cats' in topics"];
        yield 'double quotes' => ["'dogs' in topics || 'cats' in topics", '"dogs" in topics || "cats" in topics'];
    }

    public static function invalidValueProvider(): Iterator
    {
        yield 'single quotes' => ["'dogs in Topics"];
        yield 'double quotes' => ["'dogs in Topics || 'cats' in topics"];
    }
}
