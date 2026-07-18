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

        $this->assertTopicSubscriptionsEventuallyMatch($token, [
            $firstTopic => true,
            $secondTopic => true,
            $thirdTopic => true,
        ]);

        $this->messaging->unsubscribeFromTopic($firstTopic, $token);
        $this->assertTopicSubscriptionsEventuallyMatch($token, [
            $firstTopic => false,
            $secondTopic => true,
            $thirdTopic => true,
        ]);

        $this->messaging->unsubscribeFromTopic($secondTopic, $token);
        $this->assertTopicSubscriptionsEventuallyMatch($token, [
            $firstTopic => false,
            $secondTopic => false,
            $thirdTopic => true,
        ]);

        $this->messaging->unsubscribeFromAllTopics($token);
        $this->assertTopicSubscriptionsEventuallyMatch($token, [
            $firstTopic => false,
            $secondTopic => false,
            $thirdTopic => false,
        ]);
    }

    /**
     * @param non-empty-string $registrationToken
     * @param array<non-empty-string, bool> $expectedSubscriptions
     */
    private function assertTopicSubscriptionsEventuallyMatch(
        string $registrationToken,
        array $expectedSubscriptions,
    ): void {
        $this->assertEventually(
            function () use ($registrationToken, $expectedSubscriptions): bool {
                $appInstance = $this->appInstance($registrationToken);

                foreach ($expectedSubscriptions as $topic => $expectedSubscription) {
                    if ($appInstance->isSubscribedToTopic($topic) !== $expectedSubscription) {
                        return false;
                    }
                }

                return true;
            },
            10,
            'Topic subscriptions did not reach their expected state within 10 seconds.',
        );
    }

    /**
     * @param non-empty-string $registrationToken
     */
    private function appInstance(string $registrationToken): AppInstance
    {
        return $this->messaging->getAppInstance($registrationToken);
    }
}
