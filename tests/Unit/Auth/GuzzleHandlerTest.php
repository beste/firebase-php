<?php

declare(strict_types=1);

namespace Kreait\Firebase\Tests\Unit\Auth;

use Beste\Json;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use Kreait\Firebase\Auth\SignIn;
use Kreait\Firebase\Auth\SignIn\FailedToSignIn;
use Kreait\Firebase\Auth\SignIn\GuzzleHandler;
use Kreait\Firebase\Auth\SignInAnonymously;
use Kreait\Firebase\Tests\UnitTestCase;

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use const JSON_FORCE_OBJECT;

/**
 * @internal
 */
final class GuzzleHandlerTest extends UnitTestCase
{
    private MockHandler $httpResponses;

    private SignIn $action;

    private GuzzleHandler $handler;

    protected function setUp(): void
    {
        $this->httpResponses = new MockHandler();
        $this->action = SignInAnonymously::new();

        $this->handler = new GuzzleHandler('my-project', new Client(['handler' => $this->httpResponses]));
    }

    #[AllowMockObjectsWithoutExpectations]
    public function testItFailsOnAnUnsupportedAction(): void
    {
        $this->expectException(FailedToSignIn::class);
        $this->handler->handle($this->createStub(SignIn::class));
    }

    #[AllowMockObjectsWithoutExpectations]
    public function testItFailsWhenGuzzleFails(): void
    {
        $client = $this->createMock(ClientInterface::class);
        $client->method('send')->willThrowException($this->createStub(ConnectException::class));

        $handler = new GuzzleHandler('my-project', $client);

        $this->expectException(FailedToSignIn::class);
        $handler->handle($this->action);
    }

    public function testItFailsOnAnUnsuccessfulResponse(): void
    {
        $this->httpResponses->append($response = new Response(400, [], '""'));

        try {
            $this->handler->handle($this->action);
        } catch (FailedToSignIn $e) {
            $this->assertSame($response, $e->response());
            $this->assertSame($this->action, $e->action());
        }
    }

    public function testItFailsOnASuccessfulResponseWithInvalidJson(): void
    {
        $this->httpResponses->append(new Response(200, [], '{'));

        $this->expectException(FailedToSignIn::class);
        $this->handler->handle($this->action);
    }

    public function testItWorks(): void
    {
        $this->httpResponses->append(new Response(200, [], Json::encode([
            'id_token' => 'id_token',
            'refresh_token' => 'refresh_token',
            'access_token' => 'access_token',
            'expires_in' => 3600,
        ], JSON_FORCE_OBJECT)));

        $result = $this->handler->handle($this->action);

        $this->assertSame('id_token', $result->idToken());
        $this->assertSame('refresh_token', $result->refreshToken());
        $this->assertSame('access_token', $result->accessToken());
        $this->assertSame(3600, $result->ttl());
    }
}
