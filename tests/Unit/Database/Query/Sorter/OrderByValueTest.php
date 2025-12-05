<?php

declare(strict_types=1);

namespace Kreait\Firebase\Tests\Unit\Database\Query\Sorter;

use GuzzleHttp\Psr7\Uri;
use Iterator;
use Kreait\Firebase\Database\Query\Sorter\OrderByValue;
use Kreait\Firebase\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

use function rawurlencode;

/**
 * @internal
 */
final class OrderByValueTest extends UnitTestCase
{
    private OrderByValue $sorter;

    protected function setUp(): void
    {
        $this->sorter = new OrderByValue();
    }

    public function testModifyUri(): void
    {
        $this->assertStringContainsString(
            'orderBy='.rawurlencode('"$value"'),
            (string) $this->sorter->modifyUri(new Uri('https://example.com')),
        );
    }

    #[DataProvider('valueProvider')]
    public function testModifyValue(mixed $expected, mixed $given): void
    {
        $this->assertSame($expected, $this->sorter->modifyValue($given));
    }

    public static function valueProvider(): Iterator
    {
        yield 'scalar' => [
            'scalar',
            'scalar',
        ];
        yield 'array' => [
            [
                'third' => 1,
                'fourth' => 2,
                'first' => 3,
                'second' => 4,
            ],
            [
                'first' => 3,
                'second' => 4,
                'third' => 1,
                'fourth' => 2,
            ],
        ];
    }
}
