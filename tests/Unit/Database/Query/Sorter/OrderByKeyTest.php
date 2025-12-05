<?php

declare(strict_types=1);

namespace Kreait\Firebase\Tests\Unit\Database\Query\Sorter;

use GuzzleHttp\Psr7\Uri;
use Iterator;
use Kreait\Firebase\Database\Query\Sorter\OrderByKey;
use Kreait\Firebase\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

use function rawurlencode;

/**
 * @internal
 */
final class OrderByKeyTest extends UnitTestCase
{
    private OrderByKey $sorter;

    protected function setUp(): void
    {
        $this->sorter = new OrderByKey();
    }

    public function testModifyUri(): void
    {
        $this->assertStringContainsString(
            'orderBy='.rawurlencode('"$key"'),
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
                'a' => 'any',
                'b' => 'any',
                'c' => 'any',
                'd' => 'any',
            ],
            [
                'c' => 'any',
                'a' => 'any',
                'd' => 'any',
                'b' => 'any',
            ],
        ];
    }
}
