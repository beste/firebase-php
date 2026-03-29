<?php

declare(strict_types=1);

namespace Kreait\Firebase\Tests\Unit\Database;

use InvalidArgumentException;
use Iterator;
use Kreait\Firebase\Database\UrlBuilder;
use Kreait\Firebase\Tests\UnitTestCase;
use Kreait\Firebase\Util;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * @internal
 */
final class UrlBuilderTest extends UnitTestCase
{
    protected function tearDown(): void
    {
        Util::rmenv('FIREBASE_DATABASE_EMULATOR_HOST');
    }

    /**
     * @param non-empty-string $url
     */
    #[DataProvider('invalidUrls')]
    public function testWithInvalidUrl(string $url): void
    {
        $this->expectException(InvalidArgumentException::class);
        UrlBuilder::create($url);
    }

    /**
     * @return Iterator<array<int, string>>
     */
    public static function invalidUrls(): Iterator
    {
        yield 'wrong scheme' => ['ftp://example.com'];
        yield 'no scheme' => ['example.com'];
        yield 'unexpected host' => ['https://project.attacker.test'];
        yield 'url path is not allowed' => ['https://project.firebaseio.com/path'];
        yield 'query string is not allowed' => ['https://project.firebaseio.com?ns=project'];
        yield 'fragment is not allowed' => ['https://project.firebaseio.com#fragment'];
    }

    /**
     * @param non-empty-string $baseUrl
     * @param array<string, string> $queryParams
     * @param non-empty-string $expected
     */
    #[DataProvider('realUrls')]
    public function testGetGetUrl(string $baseUrl, string $path, array $queryParams, string $expected): void
    {
        $this->assertSame($expected, UrlBuilder::create($baseUrl)->getUrl($path, $queryParams));
    }

    /**
     * @param non-empty-string $emulatorHost
     * @param non-empty-string $baseUrl
     * @param array<string, string> $queryParams
     * @param non-empty-string $expected
     */
    #[DataProvider('emulatedUrls')]
    public function testEmulated(string $emulatorHost, string $baseUrl, string $path, array $queryParams, string $expected): void
    {
        Util::putenv('FIREBASE_DATABASE_EMULATOR_HOST', $emulatorHost);

        $this->assertSame($expected, UrlBuilder::create($baseUrl)->getUrl($path, $queryParams));
    }

    /**
     * @return Iterator<array<int, (array<mixed> | string)>>
     */
    public static function realUrls(): Iterator
    {
        yield 'firebaseio host, empty path, empty query' => [
            'https://project.firebaseio.com',
            '',
            [],
            'https://project.firebaseio.com/',
        ];
        yield 'firebaseio host, path without trailing slash, empty query' => [
            'https://project.firebaseio.com',
            '/path/to/child',
            [],
            'https://project.firebaseio.com/path/to/child',
        ];
        yield 'firebaseio host, path with trailing slash, empty query' => [
            'https://project.firebaseio.com',
            '/path/to/child/',
            [],
            'https://project.firebaseio.com/path/to/child',
        ];
        yield 'firebaseio host, path without trailing slash, non-empty query' => [
            'https://project.firebaseio.com',
            '/path/to/child',
            ['one' => 'two', 'three' => 'four'],
            'https://project.firebaseio.com/path/to/child?one=two&three=four',
        ];
        yield 'regional firebasedatabase host, path with trailing slash, non-empty query' => [
            'https://project.europe-west1.firebasedatabase.app',
            '/path/to/child/',
            ['one' => 'two', 'three' => 'four'],
            'https://project.europe-west1.firebasedatabase.app/path/to/child?one=two&three=four',
        ];
        yield 'regional firebasedatabase host, empty path, non-empty query' => [
            'https://project.europe-west1.firebasedatabase.app',
            '',
            ['one' => 'two', 'three' => 'four'],
            'https://project.europe-west1.firebasedatabase.app/?one=two&three=four',
        ];
    }

    /**
     * @return Iterator<array<int, (array<mixed> | string)>>
     */
    public static function emulatedUrls(): Iterator
    {
        $namespace = 'namespace';
        $baseUrl = 'https://'.$namespace.'.firebaseio.com';
        $emulatorHost = 'localhost:9000';
        yield 'empty path, empty query' => [
            $emulatorHost,
            $baseUrl,
            '',
            [],
            'http://'.$emulatorHost.'/?ns=namespace',
        ];
        yield 'path without trailing slash, empty query' => [
            $emulatorHost,
            $baseUrl,
            '/path/to/child',
            [],
            'http://'.$emulatorHost.'/path/to/child?ns=namespace',
        ];
        yield 'path with trailing slash, empty query' => [
            $emulatorHost,
            $baseUrl,
            '/path/to/child/',
            [],
            'http://'.$emulatorHost.'/path/to/child?ns=namespace',
        ];
        yield 'path without trailing slash, non-empty query' => [
            $emulatorHost,
            $baseUrl,
            '/path/to/child',
            ['one' => 'two', 'three' => 'four'],
            'http://'.$emulatorHost.'/path/to/child?ns=namespace&one=two&three=four',
        ];
        yield 'path with trailing slash, non-empty query' => [
            $emulatorHost,
            $baseUrl,
            '/path/to/child/',
            ['one' => 'two', 'three' => 'four'],
            'http://'.$emulatorHost.'/path/to/child?ns=namespace&one=two&three=four',
        ];
        yield 'empty path, non-empty query' => [
            $emulatorHost,
            $baseUrl,
            '',
            ['one' => 'two', 'three' => 'four'],
            'http://'.$emulatorHost.'/?ns=namespace&one=two&three=four',
        ];
    }
}
