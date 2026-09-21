<?php

declare(strict_types=1);

namespace Kreait\Firebase\Tests\Unit\Messaging;

use Iterator;
use Kreait\Firebase\Exception\Messaging\InvalidArgument;
use Kreait\Firebase\Messaging\FirebaseInstallationId;
use Kreait\Firebase\Messaging\FirebaseInstallationIds;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
final class FirebaseInstallationIdsTest extends TestCase
{
    #[DataProvider('validValuesWithExpectedCounts')]
    public function testItCanBeCreatedFromValues(int $expectedCount, mixed $value): void
    {
        $fids = FirebaseInstallationIds::fromValue($value);

        $this->assertCount($expectedCount, $fids);
    }

    public function testItReturnsStrings(): void
    {
        $fid = FirebaseInstallationId::fromValue('foo');

        $fids = FirebaseInstallationIds::fromValue([$fid, $fid]);
        $this->assertEqualsCanonicalizing(['foo', 'foo'], $fids->asStrings());
    }

    public function testItKnowsWhichValuesItContains(): void
    {
        $fids = FirebaseInstallationIds::fromValue(['foo']);

        $this->assertTrue($fids->has('foo'));
        $this->assertTrue($fids->has(FirebaseInstallationId::fromValue('foo')));
        $this->assertFalse($fids->has('bar'));
    }

    public function testItCannotBeEmpty(): void
    {
        $this->expectException(InvalidArgument::class);

        FirebaseInstallationIds::fromValue([]);
    }

    /**
     * @return Iterator<array<array<int, mixed>, mixed>>
     */
    public static function validValuesWithExpectedCounts(): Iterator
    {
        $foo = FirebaseInstallationId::fromValue('foo');
        yield 'string' => [1, 'foo'];
        yield 'fid object' => [1, $foo];
        yield 'collection' => [2, new FirebaseInstallationIds($foo, $foo)];
        yield 'array with mixed values' => [2, [$foo, 'bar']];
    }
}
