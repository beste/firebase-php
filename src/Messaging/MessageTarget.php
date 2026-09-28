<?php

declare(strict_types=1);

namespace Kreait\Firebase\Messaging;

use Kreait\Firebase\Exception\InvalidArgumentException;

use function mb_strtolower;

final readonly class MessageTarget
{
    public const string CONDITION = 'condition';

    public const string FID = 'fid';

    public const string TOKEN = 'token';

    public const string TOPIC = 'topic';

    /**
     * @internal
     */
    public const string UNKNOWN = 'unknown';

    /**
     * Frozen for backward compatibility: it does not include self::FID, because changing the value of a
     * public constant is a backward compatibility break. Use self::ALL_TYPES to get every target type;
     * the next major release will fold ALL_TYPES back into TYPES.
     */
    public const array TYPES = [
        self::CONDITION, self::TOKEN, self::TOPIC, self::UNKNOWN,
    ];

    /**
     * All target types, including self::FID.
     */
    public const array ALL_TYPES = [
        self::CONDITION, self::FID, self::TOKEN, self::TOPIC, self::UNKNOWN,
    ];

    /**
     * @param non-empty-string $type
     * @param non-empty-string $value
     */
    private function __construct(
        private string $type,
        private string $value,
    ) {
    }

    /**
     * Create a new message target with the given type and value.
     *
     * @param self::CONDITION|self::FID|self::TOKEN|self::TOPIC|self::UNKNOWN $type
     * @param non-empty-string $value
     *
     * @throws InvalidArgumentException
     */
    public static function with(string $type, string $value): self
    {
        $targetType = mb_strtolower($type);

        $targetValue = match ($targetType) {
            self::CONDITION => Condition::fromValue($value)->value(),
            self::FID => FirebaseInstallationId::fromValue($value)->value(),
            self::TOKEN => RegistrationToken::fromValue($value)->value(),
            self::TOPIC => Topic::fromValue($value)->value(),
            default => self::UNKNOWN,
        };

        return new self($targetType, $targetValue);
    }

    /**
     * @return non-empty-string
     */
    public function type(): string
    {
        return $this->type;
    }

    /**
     * @return non-empty-string
     */
    public function value(): string
    {
        return $this->value;
    }
}
