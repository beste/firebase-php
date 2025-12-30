<?php

declare(strict_types=1);

namespace Kreait\Firebase\Tests\Unit\Database\Query\Filter;

use GuzzleHttp\Psr7\Uri;
use Iterator;
use Kreait\Firebase\Database\Query\Filter\StartAfter;
use Kreait\Firebase\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * @internal
 */
final class StartAfterTest extends UnitTestCase
{
    #[DataProvider('valueProvider')]
    public function testModifyUri(mixed $given, mixed $expected): void
    {
        $filter = new StartAfter($given);

        $this->assertStringContainsString($expected, (string) $filter->modifyUri(new Uri('https://example.com')));
    }

    /**
     * @return Iterator<(array<int, int> | array<int, string>)>
     */
    public static function valueProvider(): Iterator
    {
        yield 'int' => [1, 'startAfter=1'];
        yield 'string' => ['value', 'startAfter=%22value%22'];
    }
}
