<?php

declare(strict_types=1);

namespace Kreait\Firebase\Tests\Integration;

use Kreait\Firebase\AppCheck\VerifyAppCheckTokenResponse;
use Kreait\Firebase\Contract\AppCheck;
use Kreait\Firebase\Contract\AppCheckWithReplayProtection;
use Kreait\Firebase\Tests\IntegrationTestCase;

/**
 * @internal
 */
final class AppCheckTest extends IntegrationTestCase
{
    public AppCheck&AppCheckWithReplayProtection $appCheck;

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

        // Replay-consumption state may not be visible immediately, so retry briefly.
        if ($secondVerification->alreadyConsumed !== true) {
            for ($attempt = 0; $attempt < 3; ++$attempt) {
                sleep(1);
                $secondVerification = $this->appCheck->verifyTokenWithReplayProtection($token->token);

                if ($secondVerification->alreadyConsumed === true) {
                    break;
                }

                $secondVerification = null;
            }
        }

        $this->assertSame(self::$appId, $firstVerification->appId);
        $this->assertSame(self::$appId, $firstVerification->token->app_id);
        $this->assertFalse($firstVerification->alreadyConsumed);
        $this->assertInstanceOf(VerifyAppCheckTokenResponse::class, $secondVerification);
        $this->assertTrue($secondVerification->alreadyConsumed);
    }
}
