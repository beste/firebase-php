<?php

declare(strict_types=1);

namespace Kreait\Firebase\Value;

use Kreait\Firebase\Exception\InvalidArgumentException;

use SensitiveParameter;
use function mb_strlen;

/**
 * @internal
 */
final readonly class ClearTextPassword
{
    /**
     * @var non-empty-string
     */
    public string $value;

    private function __construct(#[SensitiveParameter] string $value)
    {
        if ($value === '' || mb_strlen($value) < 6) {
            throw new InvalidArgumentException('A password must be a string with at least 6 characters.');
        }

        $this->value = $value;
    }

    public static function fromString(#[SensitiveParameter] string $value): self
    {
        return new self($value);
    }
}
