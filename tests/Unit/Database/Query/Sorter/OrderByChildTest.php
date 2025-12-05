<?php

declare(strict_types=1);

namespace Kreait\Firebase\Tests\Unit\Database\Query\Sorter;

use GuzzleHttp\Psr7\Uri;
use Iterator;
use Kreait\Firebase\Database\Query\Sorter\OrderByChild;
use Kreait\Firebase\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

use function rawurlencode;
use function sprintf;

/**
 * @internal
 */
final class OrderByChildTest extends UnitTestCase
{
    #[DataProvider('valueProvider')]
    public function testOrderByChild(string $childKey, mixed $expected, mixed $given): void
    {
        $sut = new OrderByChild($childKey);

        $this->assertStringContainsString(
            'orderBy='.rawurlencode(sprintf('"%s"', $childKey)),
            (string) $sut->modifyUri(new Uri('https://example.com')),
        );

        $this->assertSame($expected, $sut->modifyValue($given));
    }

    public static function valueProvider(): Iterator
    {
        yield 'scalar' => [
            'key',
            'scalar',
            'scalar',
        ];
        yield 'array' => [
            'key',
            [
                'third' => ['key' => 1],
                'fourth' => ['key' => 2],
                'first' => ['key' => 3],
                'second' => ['key' => 4],
            ],
            [
                'first' => ['key' => 3],
                'second' => ['key' => 4],
                'third' => ['key' => 1],
                'fourth' => ['key' => 2],
            ],
        ];
        yield 'nested' => [
            'child/grandchild',
            [
                'third' => ['child' => ['grandchild' => 1]],
                'fourth' => ['child' => ['grandchild' => 2]],
                'first' => ['child' => ['grandchild' => 3]],
                'second' => ['child' => ['grandchild' => 4]],
            ],
            [
                'first' => ['child' => ['grandchild' => 3]],
                'second' => ['child' => ['grandchild' => 4]],
                'third' => ['child' => ['grandchild' => 1]],
                'fourth' => ['child' => ['grandchild' => 2]],
            ],
        ];
        yield 'super_nested' => [
            'child/grandchild/great_grandchild',
            [
                'third' => ['child' => ['grandchild' => ['great_grandchild' => 1]]],
                'fourth' => ['child' => ['grandchild' => ['great_grandchild' => 2]]],
                'first' => ['child' => ['grandchild' => ['great_grandchild' => 3]]],
                'second' => ['child' => ['grandchild' => ['great_grandchild' => 4]]],
            ],
            [
                'first' => ['child' => ['grandchild' => ['great_grandchild' => 3]]],
                'second' => ['child' => ['grandchild' => ['great_grandchild' => 4]]],
                'third' => ['child' => ['grandchild' => ['great_grandchild' => 1]]],
                'fourth' => ['child' => ['grandchild' => ['great_grandchild' => 2]]],
            ],
        ];
    }
}
