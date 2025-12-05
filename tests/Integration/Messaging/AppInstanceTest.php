<?php

declare(strict_types=1);

namespace Kreait\Firebase\Tests\Integration\Messaging;

use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\AppInstance;
use Kreait\Firebase\Messaging\RegistrationToken;
use Kreait\Firebase\Tests\IntegrationTestCase;

use function bin2hex;
use function random_bytes;

/**
 * @internal
 */
final class AppInstanceTest extends IntegrationTestCase
{
    public Messaging $messaging;

    protected function setUp(): void
    {
        $this->messaging = self::$factory->createMessaging();
    }

    public function testItIsSubscribedToTopics(): void
    {
        $token = $this->getTestRegistrationToken();

        $firstTopic = bin2hex(random_bytes(5)).__FUNCTION__;
        $secondTopic = bin2hex(random_bytes(5)).__FUNCTION__;
        $thirdTopic = bin2hex(random_bytes(5)).__FUNCTION__;

        $this->messaging->subscribeToTopic($firstTopic, $token);
        $this->messaging->subscribeToTopic($secondTopic, RegistrationToken::fromValue($token)); // Lazy registration token test
        $this->messaging->subscribeToTopic($thirdTopic, $token);

        $this->assertTrue($this->appInstance($token)->isSubscribedToTopic($firstTopic));
        $this->assertTrue($this->appInstance($token)->isSubscribedToTopic($secondTopic));
        $this->assertTrue($this->appInstance($token)->isSubscribedToTopic($thirdTopic));

        $this->messaging->unsubscribeFromTopic($firstTopic, $token);
        $this->assertFalse($this->appInstance($token)->isSubscribedToTopic($firstTopic));
        $this->assertTrue($this->appInstance($token)->isSubscribedToTopic($secondTopic));
        $this->assertTrue($this->appInstance($token)->isSubscribedToTopic($thirdTopic));

        $this->messaging->unsubscribeFromTopic($secondTopic, $token);
        $this->assertFalse($this->appInstance($token)->isSubscribedToTopic($secondTopic));
        $this->assertTrue($this->appInstance($token)->isSubscribedToTopic($thirdTopic));

        $this->messaging->unsubscribeFromAllTopics($token);
        $this->assertFalse($this->appInstance($token)->isSubscribedToTopic($thirdTopic));
    }

    /**
     * @param non-empty-string $registrationToken
     */
    private function appInstance(string $registrationToken): AppInstance
    {
        return $this->messaging->getAppInstance($registrationToken);
    }
}
