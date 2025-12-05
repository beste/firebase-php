<?php

declare(strict_types=1);

namespace Kreait\Firebase\Tests\Integration;

use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use Kreait\Firebase\Contract\Auth;
use Kreait\Firebase\Exception\Auth\UserNotFound;
use Kreait\Firebase\Http\HttpClientOptions;
use Kreait\Firebase\Tests\IntegrationTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Throwable;

/**
 * @internal
 */
final class HttpLoggingTest extends IntegrationTestCase
{
    private LoggerInterface&MockObject $logger;

    private LoggerInterface&MockObject $debugLogger;

    private Auth $auth;

    private Auth $authWithLogger;

    private Auth $authWithDebugLogger;

    protected function setUp(): void
    {
        parent::setUp();

        $this->logger = $this->createMock(LoggerInterface::class);
        $this->debugLogger = $this->createMock(LoggerInterface::class);

        $logMiddleware = Middleware::log($this->logger, new MessageFormatter(), LogLevel::INFO);
        $debugLogMiddleware = Middleware::log($this->debugLogger, new MessageFormatter(MessageFormatter::DEBUG), LogLevel::DEBUG);

        $clientOptions = HttpClientOptions::default();

        $this->auth = self::$factory->createAuth();
        $this->authWithLogger = self::$factory
            ->withHttpClientOptions($clientOptions->withGuzzleMiddleware($logMiddleware))
            ->createAuth();

        $this->authWithDebugLogger = self::$factory
            ->withHttpClientOptions($clientOptions->withGuzzleMiddleware($debugLogMiddleware))
            ->createAuth();
    }

    public function testItLogsSuccesses(): void
    {
        $user = $this->auth->createAnonymousUser();

        try {
            $this->logger->expects($this->atLeastOnce())->method('log');
            $this->authWithLogger->getUser($user->uid);
        } finally {
            $this->auth->deleteUser($user->uid);
        }
    }

    public function testItLogsFailures(): void
    {
        $this->debugLogger->expects($this->atLeastOnce())->method('log');

        try {
            $this->authWithDebugLogger->updateUser('does-not-exist', []);
        } catch (Throwable $e) {
            $this->assertInstanceOf(UserNotFound::class, $e);
        }
    }

    public function testItUsesAHttpDebugLogger(): void
    {
        $user = $this->auth->createAnonymousUser();

        try {
            $this->debugLogger->expects($this->atLeastOnce())->method('log');
            $this->authWithDebugLogger->getUser($user->uid);
        } finally {
            $this->auth->deleteUser($user->uid);
        }
    }
}
