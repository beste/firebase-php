<?php

declare(strict_types=1);

namespace Kreait\Firebase\RemoteConfig;

use JsonSerializable;
use Stringable;

final readonly class UpdateType implements JsonSerializable, Stringable
{
    public const string UNSPECIFIED = 'REMOTE_CONFIG_UPDATE_TYPE_UNSPECIFIED';

    public const string INCREMENTAL_UPDATE = 'INCREMENTAL_UPDATE';

    public const string FORCED_UPDATE = 'FORCED_UPDATE';

    public const string ROLLBACK = 'ROLLBACK';

    private function __construct(private string $value)
    {
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public static function fromValue(string $value): self
    {
        return new self($value);
    }

    public function jsonSerialize(): string
    {
        return $this->value;
    }

    public function equalsTo(self|string $other): bool
    {
        return $this->value === (string) $other;
    }
}
