<?php

declare(strict_types=1);

namespace Kreait\Firebase\Tests\Unit\Exception\Messaging;

use Kreait\Firebase\Exception\Messaging\NotFound;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
final class NotFoundTest extends TestCase
{
    public function testItProvidesTheToken(): void
    {
        $exception = NotFound::becauseTokenNotFound('token');

        $this->assertSame('token', $exception->token());
    }
}
