<?php

declare(strict_types=1);

namespace Kreait\Firebase\Tests\Unit\AppCheck;

use Beste\Cache\InMemoryCache;
use Beste\Clock\FrozenClock;
use Beste\Json;
use Firebase\JWT\CachedKeySet;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\HttpFactory;
use GuzzleHttp\Psr7\Response;
use Kreait\Firebase\AppCheck;
use Kreait\Firebase\AppCheck\ApiClient;
use Kreait\Firebase\AppCheck\AppCheckTokenGenerator;
use Kreait\Firebase\AppCheck\AppCheckTokenVerifier;
use Kreait\Firebase\Exception\AppCheck\AppCheckError;
use Kreait\Firebase\Exception\AppCheck\PermissionDenied;
use Kreait\Firebase\Exception\AppCheckApiExceptionConverter;
use Kreait\Firebase\Http\ErrorResponseParser;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;

use function count;
use function parse_str;

/**
 * @internal
 */
final class DebugTokensTest extends TestCase
{
    private const string APP_ID = '1:123456789:ios:abcdef';

    private const string TOKEN_NAME = 'projects/123456789/apps/1:123456789:ios:abcdef/debugTokens/token-id';

    private MockHandler $mock;

    /** @var list<RequestInterface> */
    private array $requests = [];

    private AppCheck $appCheck;

    protected function setUp(): void
    {
        $this->mock = new MockHandler();
        $this->requests = [];

        $stack = HandlerStack::create($this->mock);
        $stack->push(Middleware::mapRequest(function (RequestInterface $request): RequestInterface {
            $this->requests[] = $request;

            return $request;
        }));

        $client = new Client([
            'handler' => $stack,
            'base_uri' => 'https://firebaseappcheck.googleapis.com/v1/projects/project-id/',
        ]);

        $apiClient = new ApiClient($client, new AppCheckApiExceptionConverter(new ErrorResponseParser()));

        $this->appCheck = new AppCheck(
            $apiClient,
            new AppCheckTokenGenerator('client@example.com', 'unused', FrozenClock::fromUTC()),
            new AppCheckTokenVerifier(
                'project-id',
                new CachedKeySet('https://example.com/jwks', $client, new HttpFactory(), new InMemoryCache()),
                $apiClient,
            ),
        );
    }

    public function testCreateDebugTokenWithGivenToken(): void
    {
        $this->mock->append(new Response(200, [], Json::encode([
            'name' => self::TOKEN_NAME,
            'displayName' => 'CI',
            'updateTime' => '2026-11-23T10:00:00.123456Z',
        ])));

        $secret = '1d7e7c8a-4f1b-4c2e-9a3d-5b6c7d8e9f00';
        $debugToken = $this->appCheck->createDebugToken(self::APP_ID, 'CI', $secret);

        $this->assertSame(self::TOKEN_NAME, $debugToken->name);
        $this->assertSame('token-id', $debugToken->id);
        $this->assertSame('CI', $debugToken->displayName);
        $this->assertSame($secret, $debugToken->token);
        $this->assertSame('2026-11-23 10:00:00', $debugToken->updateTime?->format('Y-m-d H:i:s'));

        $request = $this->lastRequest();
        $this->assertSame('POST', $request->getMethod());
        $this->assertStringEndsWith('/apps/'.self::APP_ID.'/debugTokens', $request->getUri()->getPath());
        $this->assertSame(['displayName' => 'CI', 'token' => $secret], Json::decode((string) $request->getBody(), true));
    }

    public function testCreateDebugTokenGeneratesUuid4WhenNoTokenIsGiven(): void
    {
        $this->mock->append(new Response(200, [], Json::encode(['name' => self::TOKEN_NAME, 'displayName' => 'CI'])));

        $debugToken = $this->appCheck->createDebugToken(self::APP_ID, 'CI');

        /** @var array{token: string} $body */
        $body = Json::decode((string) $this->lastRequest()->getBody(), true);

        $this->assertMatchesRegularExpression('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/', $body['token']);
        $this->assertSame($body['token'], $debugToken->token);
        $this->assertNull($debugToken->updateTime);
    }

    public function testGetDebugToken(): void
    {
        $this->mock->append(new Response(200, [], Json::encode(['name' => self::TOKEN_NAME, 'displayName' => 'CI'])));

        $debugToken = $this->appCheck->getDebugToken(self::APP_ID, 'token-id');

        $this->assertSame('token-id', $debugToken->id);
        $this->assertNull($debugToken->token);

        $request = $this->lastRequest();
        $this->assertSame('GET', $request->getMethod());
        $this->assertStringEndsWith('/apps/'.self::APP_ID.'/debugTokens/token-id', $request->getUri()->getPath());
    }

    public function testListDebugTokensFollowsPagination(): void
    {
        $this->mock->append(
            new Response(200, [], Json::encode([
                'debugTokens' => [['name' => self::TOKEN_NAME, 'displayName' => 'First']],
                'nextPageToken' => 'next',
            ])),
            new Response(200, [], Json::encode([
                'debugTokens' => [['name' => self::TOKEN_NAME.'2', 'displayName' => 'Second']],
            ])),
        );

        $tokens = $this->appCheck->listDebugTokens(self::APP_ID);

        $this->assertCount(2, $tokens);
        $this->assertSame('First', $tokens[0]->displayName);
        $this->assertSame('Second', $tokens[1]->displayName);

        $this->assertSame('', $this->requests[0]->getUri()->getQuery());
        $this->assertSame('pageToken=next', $this->requests[1]->getUri()->getQuery());
    }

    public function testListDebugTokensWithEmptyResponse(): void
    {
        $this->mock->append(new Response(200, [], '{}'));

        $this->assertSame([], $this->appCheck->listDebugTokens(self::APP_ID));
    }

    public function testUpdateDebugToken(): void
    {
        $this->mock->append(new Response(200, [], Json::encode(['name' => self::TOKEN_NAME, 'displayName' => 'Renamed'])));

        $debugToken = $this->appCheck->updateDebugToken(self::APP_ID, 'token-id', 'Renamed');

        $this->assertSame('Renamed', $debugToken->displayName);

        $request = $this->lastRequest();
        $this->assertSame('PATCH', $request->getMethod());
        $this->assertStringEndsWith('/apps/'.self::APP_ID.'/debugTokens/token-id', $request->getUri()->getPath());
        parse_str($request->getUri()->getQuery(), $query);
        $this->assertSame(['updateMask' => 'displayName'], $query);
        $this->assertSame(['displayName' => 'Renamed'], Json::decode((string) $request->getBody(), true));
    }

    public function testDeleteDebugToken(): void
    {
        $this->mock->append(new Response(200, [], '{}'));

        $this->appCheck->deleteDebugToken(self::APP_ID, 'token-id');

        $request = $this->lastRequest();
        $this->assertSame('DELETE', $request->getMethod());
        $this->assertStringEndsWith('/apps/'.self::APP_ID.'/debugTokens/token-id', $request->getUri()->getPath());
    }

    public function testApiErrorsAreConverted(): void
    {
        $this->mock->append(new Response(403, [], Json::encode(['error' => ['message' => 'Denied']])));

        $this->expectException(PermissionDenied::class);

        $this->appCheck->createDebugToken(self::APP_ID, 'CI');
    }

    public function testNotFoundIsConvertedToAppCheckError(): void
    {
        $this->mock->append(new Response(404, [], Json::encode(['error' => ['message' => 'Not found']])));

        $this->expectException(AppCheckError::class);

        $this->appCheck->getDebugToken(self::APP_ID, 'missing');
    }

    private function lastRequest(): RequestInterface
    {
        $this->assertNotSame([], $this->requests);

        return $this->requests[count($this->requests) - 1];
    }
}
