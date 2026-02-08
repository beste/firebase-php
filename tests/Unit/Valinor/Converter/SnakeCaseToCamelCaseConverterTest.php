<?php

declare(strict_types=1);

namespace Kreait\Firebase\Tests\Unit\Valinor\Converter;

use Kreait\Firebase\Valinor\Converter\SnakeCaseToCamelCaseConverter;
use PHPUnit\Framework\TestCase;

final class SnakeCaseToCamelCaseConverterTest extends TestCase
{
    public function testItConvertsSnakeCaseKeysToCamelCaseBeforePassingValuesToTheNextConverter(): void
    {
        $converter = new SnakeCaseToCamelCaseConverter();
        $nextResult = new class() {};
        $received = null;

        $result = $converter(
            ['first_name' => 'Jane', 'user_id' => 1],
            static function (iterable $values) use (&$received, $nextResult): object {
                $received = $values;

                return $nextResult;
            },
        );

        $this->assertSame(['firstName' => 'Jane', 'userId' => 1], $received);
        $this->assertSame($nextResult, $result);
    }

    public function testItSupportsTraversables(): void
    {
        $converter = new SnakeCaseToCamelCaseConverter();
        $received = null;

        $values = (static function (): iterable {
            yield 'tenant_id' => 'tenant';
            yield 'display_name' => 'Jane';
        })();

        $converter(
            $values,
            static function (iterable $values) use (&$received): object {
                $received = $values;

                return new class() {};
            },
        );

        $this->assertSame(['tenantId' => 'tenant', 'displayName' => 'Jane'], $received);
    }
}
