<?php

declare(strict_types=1);

namespace Kreait\Firebase\Tests\Unit\Http;

use GuzzleHttp\Promise\PromiseInterface;
use InvalidArgumentException;
use Kreait\Firebase\Http\HttpClientOptions;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;

/**
 * @internal
 */
final class HttpClientOptionsTest extends TestCase
{
    public function testOptionsCanBeSet(): void
    {
        $options = HttpClientOptions::default()
            ->withConnectTimeout(1.1)
            ->withReadTimeout(2.2)
            ->withTimeout(3.3)
            ->withProxy('https://proxy.example.com')
        ;

        $this->assertEqualsWithDelta(1.1, $options->connectTimeout(), PHP_FLOAT_EPSILON);
        $this->assertEqualsWithDelta(2.2, $options->readTimeout(), PHP_FLOAT_EPSILON);
        $this->assertEqualsWithDelta(3.3, $options->timeout(), PHP_FLOAT_EPSILON);
        $this->assertSame('https://proxy.example.com', $options->proxy());
    }

    public function testConnectTimeoutMustBePositive(): void
    {
        $this->expectException(InvalidArgumentException::class);
        HttpClientOptions::default()->withConnectTimeout(-0.1);
    }

    public function testReadTimeoutMustBePositive(): void
    {
        $this->expectException(InvalidArgumentException::class);
        HttpClientOptions::default()->withReadTimeout(-0.1);
    }

    public function testTimeoutMustBePositive(): void
    {
        $this->expectException(InvalidArgumentException::class);
        HttpClientOptions::default()->withTimeout(-0.1);
    }

    public function testItAcceptsSingleGuzzleClientConfigOptions(): void
    {
        $options = HttpClientOptions::default()->withGuzzleConfigOption('foo', 'bar');

        $this->assertEqualsCanonicalizing(['foo' => 'bar'], $options->guzzleConfig());
    }

    public function testItAcceptsMultipleGuzzleClientConfigOptions(): void
    {
        $options = HttpClientOptions::default()->withGuzzleConfigOptions([
            'first' => 'first value',
            'second' => 'second value',
        ]);

        $this->assertEqualsCanonicalizing(
            [
                'first' => 'first value',
                'second' => 'second value',
            ],
            $options->guzzleConfig(),
        );
    }

    public function testItRetainsPreviouslySetGuzzleConfigOptions(): void
    {
        $options = HttpClientOptions::default()
            ->withGuzzleConfigOption('existing', 'existing')
            ->withGuzzleConfigOptions(['new' => 'new'])
        ;

        $this->assertEqualsCanonicalizing(
            [
                'existing' => 'existing',
                'new' => 'new',
            ],
            $options->guzzleConfig(),
        );
    }

    public function testItAcceptsSingleCallableMiddlewares(): void
    {
        $options = HttpClientOptions::default()->withGuzzleMiddleware(static fn(): string => 'Foo', 'name');

        $middlewares = $options->guzzleMiddlewares();

        $this->assertCount(1, $middlewares);
    }

    public function testItAcceptsMultipleMiddlewares(): void
    {
        $middlewareClass = new class {
            public static function handle(): void
            {
                // This is just a placeholder to demonstrate a callable middleware
            }
        };
        $options = HttpClientOptions::default()
            ->withGuzzleMiddlewares([
                static fn(): string => 'Foo',
                ['middleware' => static fn(): string => 'Foo', 'name' => 'Foo'],
                ['middleware' => [$middlewareClass::class, 'handle'], 'name' => 'Bar'],
            ])
        ;

        $middlewares = $options->guzzleMiddlewares();

        $this->assertCount(3, $middlewares);

        $this->assertSame('', $middlewares[0]['name']);
        $this->assertSame('Foo', $middlewares[1]['name']);
        $this->assertSame('Bar', $middlewares[2]['name']);
    }

    public function testItAcceptsACustomHandler(): void
    {
        $handler = fn(RequestInterface $request, array $options): PromiseInterface => $this->createMock(PromiseInterface::class);

        $options = HttpClientOptions::default()->withGuzzleHandler($handler);

        $config = $options->guzzleConfig();

        $this->assertArrayHasKey('handler', $config);
        $this->assertSame($handler, $config['handler']);
    }
}
