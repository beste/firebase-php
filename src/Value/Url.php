<?php

declare(strict_types=1);

namespace Kreait\Firebase\Value;

use Kreait\Firebase\Exception\InvalidArgumentException;

/**
 * @internal
 */
final readonly class Url
{
    /**
     * @var non-empty-string
     */
    public string $value;

    /**
     * @param non-empty-string $value
     */
    private function __construct(string $value)
    {
        $startsWithHttp = str_starts_with($value, 'https://') || str_starts_with($value, 'http://');
        $parsedValue = parse_url($value);

        if (!$startsWithHttp || $parsedValue === false) {
            throw new InvalidArgumentException('The URL is invalid.');
        }

        $this->value = $value;
    }

    public static function fromString(string $value): self
    {
        if ($value === '') {
            throw new InvalidArgumentException('The URL cannot be empty.');
        }

        return new self($value);
    }
}
