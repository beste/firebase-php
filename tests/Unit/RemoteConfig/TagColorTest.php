<?php

declare(strict_types=1);

namespace Kreait\Firebase\Tests\Unit\RemoteConfig;

use Kreait\Firebase\Exception\InvalidArgumentException;
use Kreait\Firebase\RemoteConfig\TagColor;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
final class TagColorTest extends TestCase
{
    public function testCreateWithInvalidValue(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new TagColor('foo');
    }
}
