<?php

declare(strict_types=1);

namespace Kreait\Firebase\Tests\Unit\Messaging;

use Beste\Json;
use Iterator;
use Kreait\Firebase\Messaging\FirebaseInstallationId;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
final class FirebaseInstallationIdTest extends TestCase
{
    #[DataProvider('valueProvider')]
    public function testFromValue(string $expected, string $value): void
    {
        $fid = FirebaseInstallationId::fromValue($value);

        $this->assertSame($expected, $fid->value());
        $this->assertSame('"'.$fid.'"', Json::encode($fid));
    }

    /**
     * @return Iterator<array<int, string>>
     */
    public static function valueProvider(): Iterator
    {
        yield 'foo' => ['foo', 'foo'];
    }
}
