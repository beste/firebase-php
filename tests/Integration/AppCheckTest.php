<?php

declare(strict_types=1);

namespace Kreait\Firebase\Tests\Integration;

use Kreait\Firebase\AppCheck\DebugToken;
use Kreait\Firebase\Contract\AppCheck;
use Kreait\Firebase\Contract\AppCheckWithDebugTokens;
use Kreait\Firebase\Contract\AppCheckWithReplayProtection;
use Kreait\Firebase\Tests\IntegrationTestCase;

use function array_map;

/**
 * @internal
 */
final class AppCheckTest extends IntegrationTestCase
{
    public AppCheck&AppCheckWithReplayProtection&AppCheckWithDebugTokens $appCheck;

    protected function setUp(): void
    {
        parent::setUp();

        if (self::$appId === null) {
            $this->markTestSkipped('AppCheck tests require an App ID');
        }

        $this->appCheck = self::$factory->createAppCheck();
    }

    public function testCreateTokenWithDefaultTtl(): void
    {
        $token = $this->appCheck->createToken(self::$appId);

        $this->assertSame('3600s', $token->ttl);
    }

    public function testCreateTokenWithCustomTtl(): void
    {
        $token = $this->appCheck->createToken(self::$appId, ['ttl' => 1800]);

        $this->assertSame('1800s', $token->ttl);
    }

    public function testVerifyToken(): void
    {
        $token = $this->appCheck->createToken(self::$appId);

        $response = $this->appCheck->verifyToken($token->token);

        $this->assertSame(self::$appId, $response->appId);
        $this->assertSame(self::$appId, $response->token->app_id);
    }

    public function testVerifyTokenWithReplayProtection(): void
    {
        $token = $this->appCheck->createToken(self::$appId);

        $firstVerification = $this->appCheck->verifyTokenWithReplayProtection($token->token);
        $secondVerification = $this->appCheck->verifyTokenWithReplayProtection($token->token);

        // Replay-consumption state may not be visible immediately.
        $this->assertEventually(function () use ($token, &$secondVerification): bool {
            if ($secondVerification->alreadyConsumed === true) {
                return true;
            }

            $secondVerification = $this->appCheck->verifyTokenWithReplayProtection($token->token);

            return $secondVerification->alreadyConsumed === true;
        }, 3, 'Replay-consumption state did not become visible within 3 seconds.');

        $this->assertSame(self::$appId, $firstVerification->appId);
        $this->assertSame(self::$appId, $firstVerification->token->app_id);
        $this->assertFalse($firstVerification->alreadyConsumed);
        $this->assertTrue($secondVerification->alreadyConsumed);
    }

    public function testDebugTokenLifecycle(): void
    {
        $debugToken = $this->appCheck->createDebugToken(self::$appId, 'Integration Test');

        try {
            $this->assertNotNull($debugToken->token);
            $this->assertSame('Integration Test', $debugToken->displayName);

            $fetched = $this->appCheck->getDebugToken(self::$appId, $debugToken->id);
            $this->assertSame($debugToken->name, $fetched->name);
            $this->assertNull($fetched->token);

            $names = array_map(static fn(DebugToken $token): string => $token->name, $this->appCheck->listDebugTokens(self::$appId));
            $this->assertContains($debugToken->name, $names);

            $updated = $this->appCheck->updateDebugToken(self::$appId, $debugToken->id, 'Integration Test (renamed)');
            $this->assertSame('Integration Test (renamed)', $updated->displayName);
        } finally {
            $this->appCheck->deleteDebugToken(self::$appId, $debugToken->id);
        }
    }
}
