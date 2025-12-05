<?php

declare(strict_types=1);

namespace Kreait\Firebase\Value;

use Kreait\Firebase\Exception\InvalidArgumentException;

use function filter_var;

use const FILTER_VALIDATE_EMAIL;

/**
 * @internal
 */
final readonly class Email
{
    /**
     * @var non-empty-string
     */
    public string $value;

    private function __construct(string $value)
    {
        if ($value === '' || filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
            throw new InvalidArgumentException('The email address is invalid.');
        }

        $this->value = $value;
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }
}
