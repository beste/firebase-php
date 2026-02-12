<?php

declare(strict_types=1);

namespace Kreait\Firebase\Tests\Unit\Valinor\Transformer;

use Kreait\Firebase\Valinor\Transformer\CamelToSnakeCaseTransformer;
use PHPUnit\Framework\TestCase;

final class CamelToSnakeCaseTransformerTest extends TestCase
{
    public function testItConvertsCamelCaseKeysToSnakeCase(): void
    {
        $transformer = new CamelToSnakeCaseTransformer();

        $result = $transformer(
            new class() {},
            static fn(): array => ['firstName' => 'Jane', 'URLValue' => 'value'],
        );

        $this->assertSame(['first_name' => 'Jane', 'u_r_l_value' => 'value'], $result);
    }

    public function testItKeepsNonStringKeysUntouched(): void
    {
        $transformer = new CamelToSnakeCaseTransformer();

        $result = $transformer(
            new class() {},
            static fn(): array => [10 => 'ten', 'userName' => 'jane'],
        );

        $this->assertSame([10 => 'ten', 'user_name' => 'jane'], $result);
    }

    public function testItReturnsNonIterableValuesWithoutChanges(): void
    {
        $transformer = new CamelToSnakeCaseTransformer();
        $resultFromNext = (object) ['name' => 'Jane'];

        $result = $transformer(
            new class() {},
            static fn() => $resultFromNext,
        );

        $this->assertSame($resultFromNext, $result);
    }
}
