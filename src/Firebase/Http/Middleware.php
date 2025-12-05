<?php

declare(strict_types=1);

namespace Kreait\Firebase\Http;

use Beste\Json;
use Closure;
use GuzzleHttp\Psr7\Query;
use Psr\Http\Message\RequestInterface;

use function array_merge;
use function ltrim;
use function str_ends_with;

/**
 * @internal
 */
final class Middleware
{
    /**
     * Ensures that the ".json" suffix is added to URIs and that the content type is set correctly.
     *
     * @return callable(callable): Closure
     */
    public static function ensureJsonSuffix(): callable
    {
        return static fn(callable $handler): Closure => static function (RequestInterface $request, ?array $options = null) use ($handler) {
            $uri = $request->getUri();
            $path = '/'.ltrim($uri->getPath(), '/');

            if (!str_ends_with($path, '.json')) {
                $uri = $uri->withPath($path.'.json');
                $request = $request->withUri($uri);
            }

            return $handler($request, $options);
        };
    }

    /**
     * @param array<string, mixed>|null $override
     *
     * @return callable(callable): Closure
     */
    public static function addDatabaseAuthVariableOverride(?array $override): callable
    {
        return static fn(callable $handler): Closure => static function (RequestInterface $request, ?array $options = null) use ($handler, $override) {
            $uri = $request->getUri();

            $uri = $uri->withQuery(Query::build(
                array_merge(Query::parse($uri->getQuery()), ['auth_variable_override' => Json::encode($override)]),
            ));

            return $handler($request->withUri($uri), $options);
        };
    }
}
