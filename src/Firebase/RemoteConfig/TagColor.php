<?php

declare(strict_types=1);

namespace Kreait\Firebase\RemoteConfig;

use Kreait\Firebase\Exception\InvalidArgumentException;
use Stringable;

use function implode;
use function in_array;
use function mb_strtoupper;
use function sprintf;

class TagColor implements Stringable
{
    final public const string BLUE = 'BLUE';

    final public const string BROWN = 'BROWN';

    final public const string CYAN = 'CYAN';

    final public const string DEEP_ORANGE = 'DEEP_ORANGE';

    final public const string GREEN = 'GREEN';

    final public const string INDIGO = 'INDIGO';

    final public const string LIME = 'LIME';

    final public const string ORANGE = 'ORANGE';

    final public const string PINK = 'PINK';

    final public const string PURPLE = 'PURPLE';

    final public const string TEAL = 'TEAL';

    final public const array VALID_COLORS = [
        self::BLUE, self::BROWN, self::CYAN, self::DEEP_ORANGE, self::GREEN, self::INDIGO, self::LIME,
        self::ORANGE, self::PINK, self::PURPLE, self::TEAL,
    ];

    /**
     * @var non-empty-string
     */
    private readonly string $value;

    /**
     * @param non-empty-string $value
     */
    public function __construct(string $value)
    {
        $value = mb_strtoupper($value);

        if (!in_array($value, self::VALID_COLORS, true)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Invalid tag color "%s". Supported colors are "%s".',
                    $value,
                    implode('", "', self::VALID_COLORS),
                ),
            );
        }

        $this->value = $value;
    }

    /**
     * @return non-empty-string
     */
    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * @return non-empty-string
     */
    public function value(): string
    {
        return $this->value;
    }
}
