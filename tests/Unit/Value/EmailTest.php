<?php

declare(strict_types=1);

namespace Kreait\Firebase\Tests\Unit\Value;

use Iterator;
use Kreait\Firebase\Exception\InvalidArgumentException;
use Kreait\Firebase\Value\Email;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
final class EmailTest extends TestCase
{
    #[DataProvider('validValues')]
    public function testWithValidValue(string $value): void
    {
        $email = Email::fromString($value)->value;

        $this->assertSame($value, $email);
    }

    #[DataProvider('invalidValues')]
    public function testWithInvalidValue(string $value): void
    {
        $this->expectException(InvalidArgumentException::class);
        Email::fromString($value);
    }

    /**
     * @return Iterator<array<int, string>>
     */
    public static function validValues(): Iterator
    {
        yield 'user@example.com' => ['user@example.com'];
    }

    /**
     * @return Iterator<array<int, string>>
     */
    public static function invalidValues(): Iterator
    {
        yield 'empty string' => [''];
        yield 'invalid' => ['invalid'];
    }
}
