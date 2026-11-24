<?php

declare(strict_types=1);

namespace Kreait\Firebase\Tests\Unit\AppCheck;

use Kreait\Firebase\AppCheck\AppCheckToken;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
final class AppCheckTokenTest extends TestCase
{
    public function testCreateFromValidArray(): void
    {
        $options = AppCheckToken::fromArray([
            'ttl' => $ttl = '300',
            'token' => $token = 'jwtToken',
        ]);

        $this->assertSame($ttl, $options->ttl);
        $this->assertSame($token, $options->token);
    }
}
