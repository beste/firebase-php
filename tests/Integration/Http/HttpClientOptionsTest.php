<?php

declare(strict_types=1);

namespace Kreait\Firebase\Tests\Integration\Http;

use Closure;
use Kreait\Firebase\Contract\Database;
use Kreait\Firebase\Http\HttpClientOptions;
use Kreait\Firebase\Tests\IntegrationTestCase;
use PHPUnit\Framework\Attributes\Test;
use Psr\Http\Message\RequestInterface;

/**
 * @internal
 */
final class HttpClientOptionsTest extends IntegrationTestCase
{
    #[Test]
    public function itWorksWithAFunctionMiddleware(): void
    {
        $check = false;

        $middleware = static function (callable $handler) use (&$check): Closure {
            return static function (RequestInterface $request, array $options) use ($handler, &$check) {
                $check = true;

                return $handler($request, $options);
            };
        };

        $this
            ->db(HttpClientOptions::default()->withGuzzleMiddleware($middleware))
            ->getReference(__FUNCTION__)
            ->getSnapshot();

        $this->assertTrue($check);
    }

    #[Test]
    public function itWorksWithAFunctionMiddlewareInsideAnArrayDefinition(): void
    {
        $check = false;

        $middleware = static function (callable $handler) use (&$check): Closure {
            return static function (RequestInterface $request, array $options) use ($handler, &$check) {
                $check = true;

                return $handler($request, $options);
            };
        };

        $definition = [
            'middleware' => $middleware,
            'name' => 'test',
        ];

        $this
            ->db(HttpClientOptions::default()->withGuzzleMiddlewares([$definition]))
            ->getReference(__FUNCTION__)
            ->getSnapshot();

        $this->assertTrue($check);
    }

    #[Test]
    public function itWorksWithAMiddlewareClassWithAStaticMethod(): void
    {
        $middleware = new class {
            public static bool $wasInvoked = false;

            public function __construct()
            {
                // Reset the static property to ensure a clean state for each test
                self::$wasInvoked = false;
            }

            public static function handle(callable $handler): Closure
            {
                return static function ($request, ?array $options = null) use ($handler) {
                    self::$wasInvoked = true;

                    return $handler($request, $options);
                };
            }
        };

        $this
            ->db(HttpClientOptions::default()->withGuzzleMiddleware([$middleware::class, 'handle']))
            ->getReference(__FUNCTION__)
            ->getSnapshot();

        $this->assertTrue($middleware::$wasInvoked);
    }

    #[Test]
    public function itWorksWithAnInvokableMiddleware(): void
    {
        $middleware = new class {
            public bool $wasInvoked = false;

            public function __invoke(callable $handler): Closure
            {
                return function (RequestInterface $request, ?array $options = null) use ($handler) {
                    $this->wasInvoked = true;

                    return $handler($request, $options);
                };
            }
        };

        $this
            ->db(HttpClientOptions::default()->withGuzzleMiddleware($middleware))
            ->getReference(__FUNCTION__)
            ->getSnapshot();

        $this->assertTrue($middleware->wasInvoked);
    }

    private function db(HttpClientOptions $options): Database
    {
        if (self::$rtdbUrl === null) {
            $this->markTestSkipped('The HTTP client options test requires a database URL');
        }

        return self::$factory
            ->withDatabaseUri(self::$rtdbUrl)
            ->withHttpClientOptions($options)
            ->createDatabase();
    }
}
