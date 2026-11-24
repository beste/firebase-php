<?php

declare(strict_types=1);

namespace Kreait\Firebase\Tests\Unit\Database\Query\Filter;

use GuzzleHttp\Psr7\Uri;
use Iterator;
use Kreait\Firebase\Database\Query\Filter\EqualTo;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
final class EqualToTest extends TestCase
{
    #[DataProvider('valueProvider')]
    public function testModifyUri(mixed $given, string $expected): void
    {
        $filter = new EqualTo($given);

        $this->assertStringContainsString($expected, (string) $filter->modifyUri(new Uri('https://example.com')));
    }

    /**
     * @return Iterator<(array<int, int> | array<int, string>)>
     */
    public static function valueProvider(): Iterator
    {
        yield 'int' => [1, 'equalTo=1'];
        yield 'string' => ['value', 'equalTo=%22value%22'];
    }
}
