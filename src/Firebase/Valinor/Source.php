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

    /**
     * @param iterable<mixed>|string $value
     */
    public static function parse(iterable|string $value): self
    {
        if (is_iterable($value)) {
            return new self(BaseSource::iterable($value));
        }

        if (str_starts_with($value, '{') || str_starts_with($value, '[')) {
            return self::json($value);
        }

        return self::file($value);
    }

    private static function json(string $value): self
    {
        try {
            return new self(BaseSource::json($value));
        } catch (Throwable $e) {
            throw new InvalidArgumentException(message: $e->getMessage(), previous: $e);
        }
    }

    public static function file(string $value): self
    {
        try {
            $file = new SplFileObject($value);
        } catch (Throwable $e) {
            throw new InvalidArgumentException(message: $e->getMessage(), previous: $e);
        }

        $content = $file->fread($file->getSize());
        $pathName = $file->getPathname();

        if ($content === false) {
            throw new InvalidArgumentException("Unable to parse `$pathName`");
        }

        return self::json($content);
    }

    public function getIterator(): Traversable
    {
        yield from $this->delegate;
    }
}
