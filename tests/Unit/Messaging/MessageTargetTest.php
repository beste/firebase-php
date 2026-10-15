<?php

declare(strict_types=1);

namespace Kreait\Firebase\Tests\Unit\Messaging;

use Kreait\Firebase\Messaging\MessageTarget;
use Kreait\Firebase\Tests\UnitTestCase;

/**
 * @internal
 */
final class MessageTargetTest extends UnitTestCase
{
    public function testItCreatesAFidTarget(): void
    {
        $target = MessageTarget::with(MessageTarget::FID, 'a-fid');

        $this->assertSame(MessageTarget::FID, $target->type());
        $this->assertSame('a-fid', $target->value());
    }

    public function testAnUnknownTypeResultsInAnUnknownTarget(): void
    {
        $target = MessageTarget::with('something-else', 'a-value');

        $this->assertSame(MessageTarget::UNKNOWN, $target->value());
    }
}
