<?php

declare(strict_types=1);

namespace Kreait\Firebase\Valinor\Converter;

use Traversable;

/**
 * @internal
 */
final readonly class UnwrapIterableByKey
{
    public function __construct(private int|string $key)
    {
    }

    /**
     * @template T
     * @param iterable<mixed> $values
     * @param callable(iterable<mixed>): T $next
     * @return T
     */
    public function __invoke(iterable $values, callable $next): mixed
    {
        if ($values instanceof Traversable) {
            $values = iterator_to_array($values);
        }

        if (array_key_exists($this->key, $values) && is_iterable($values[$this->key])) {
            return $next($values[$this->key]);
        }

        return $next($values);
    }
}
