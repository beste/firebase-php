<?php

declare(strict_types=1);

namespace Kreait\Firebase\Tests\Unit\Value;

use Iterator;
use Kreait\Firebase\Exception\InvalidArgumentException;
use Kreait\Firebase\Value\ClearTextPassword;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
final class ClearTextPasswordTest extends TestCase
{
    #[DataProvider('validValues')]
    public function testWithValidValue(mixed $value): void
    {
        $password = ClearTextPassword::fromString($value)->value;

        $this->assertSame($value, $password);
    }

    #[DataProvider('invalidValues')]
    public function testWithInvalidValue(mixed $value): void
    {
        $this->expectException(InvalidArgumentException::class);
        ClearTextPassword::fromString($value);
    }

    /**
     * @return Iterator<array<int, string>>
     */
    public static function validValues(): Iterator
    {
        yield 'long enough' => ['long enough'];
    }

    /**
     * @return Iterator<array<int, string>>
     */
    public static function invalidValues(): Iterator
    {
        yield 'empty string' => [''];
        yield 'less than 6 chars' => ['short'];
    }
}
