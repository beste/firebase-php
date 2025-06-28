<?php

declare(strict_types=1);

namespace Kreait\Firebase\Valinor;

use CuyZ\Valinor\Mapper\Source\Source as BaseSource;
use IteratorAggregate;
use Kreait\Firebase\Exception\InvalidArgumentException;
use SplFileObject;
use Throwable;
use Traversable;

/**
 * @internal
 *
 * @implements IteratorAggregate<mixed>
 */
final class Source implements IteratorAggregate
{
    private function __construct(
        /** @var iterable<mixed> */
        private readonly iterable $delegate,
    ) {
    }

    public static function parse(mixed $value): self
    {
        if (is_iterable($value)) {
            return new self(BaseSource::iterable($value));
        }

        if (str_starts_with((string) $value, '{')) {
            try {
                return new self(BaseSource::json($value));
            } catch (Throwable $e) {
                throw new InvalidArgumentException(message: $e->getMessage(), previous: $e);
            }
        }

        try {
            return new self(BaseSource::file(new SplFileObject($value)));
        } catch (Throwable $e) {
            throw new InvalidArgumentException(message: $e->getMessage(), previous: $e);
        }
    }

    public function getIterator(): Traversable
    {
        yield from $this->delegate;
    }
}
