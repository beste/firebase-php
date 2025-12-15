<?php

declare(strict_types=1);

namespace Kreait\Firebase\Database\Query\Sorter;

use Kreait\Firebase\Database\Query\Sorter;
use Psr\Http\Message\UriInterface;

/**
 * @internal
 */
final readonly class Noop implements Sorter
{
    public function modifyUri(UriInterface $uri): UriInterface
    {
        return $uri;
    }

    public function modifyValue(mixed $value): mixed
    {
        return $value;
    }
}
