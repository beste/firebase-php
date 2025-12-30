<?php

declare(strict_types=1);

namespace Kreait\Firebase\RemoteConfig;

use JsonSerializable;

use function is_bool;
use function is_string;

/**
 * @phpstan-import-type RemoteConfigParameterValueShape from ParameterValue
 *
 * @phpstan-type RemoteConfigParameterShape array{
 *     description?: string|null,
 *     defaultValue?: RemoteConfigParameterValueShape|null,
 *     conditionalValues?: array<non-empty-string, RemoteConfigParameterValueShape>|null,
 *     valueType?: non-empty-string|null
 * }
 */
readonly class Parameter implements JsonSerializable
{
    /**
     * @param non-empty-string $name
     * @param list<ConditionalValue> $conditionalValues
     */
    private function __construct(
        private string $name,
        private string $description,
        private ?ParameterValue $defaultValue,
        private array $conditionalValues,
        private ParameterValueType $valueType,
    ) {
    }

    /**
     * @param non-empty-string $name
     * @param RemoteConfigParameterValueShape|string|bool|null $defaultValue
     */
    public static function named(string $name, array|string|bool|null $defaultValue = null, ?ParameterValueType $valueType = null): self
    {
        $defaultValue = self::mapDefaultValue($defaultValue);

        return new self(
            name: $name,
            description: '',
            defaultValue: $defaultValue,
            conditionalValues: [],
            valueType: $valueType ?? ParameterValueType::UNSPECIFIED,
        );
    }

    /**
     * @param RemoteConfigParameterValueShape|string|bool|null $defaultValue
     */
    private static function mapDefaultValue(array|string|bool|null $defaultValue): ?ParameterValue
    {
        if ($defaultValue === null) {
            return null;
        }

        if (is_string($defaultValue)) {
            return ParameterValue::withValue($defaultValue);
        }

        if (is_bool($defaultValue)) {
            return ParameterValue::inAppDefault();
        }

        return ParameterValue::fromArray($defaultValue);
    }

    /**
     * @return non-empty-string
     */
    public function name(): string
    {
        return $this->name;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function withDescription(string $description): self
    {
        return new self(
            name: $this->name,
            description: $description,
            defaultValue: $this->defaultValue,
            conditionalValues: $this->conditionalValues,
            valueType: $this->valueType,
        );
    }

    /**
     * @param RemoteConfigParameterValueShape|string|bool|null $defaultValue
     */
    public function withDefaultValue(array|string|bool|null $defaultValue): self
    {
        $defaultValue = self::mapDefaultValue($defaultValue);

        return new self(
            name: $this->name,
            description: $this->description,
            defaultValue: $defaultValue,
            conditionalValues: $this->conditionalValues,
            valueType: $this->valueType,
        );
    }

    public function defaultValue(): ?ParameterValue
    {
        return $this->defaultValue;
    }

    public function withConditionalValue(ConditionalValue $conditionalValue): self
    {
        $conditionalValues = $this->conditionalValues;
        $conditionalValues[] = $conditionalValue;

        return new self(
            name: $this->name,
            description: $this->description,
            defaultValue: $this->defaultValue,
            conditionalValues: $conditionalValues,
            valueType: $this->valueType,
        );
    }

    /**
     * @return list<ConditionalValue>
     */
    public function conditionalValues(): array
    {
        return $this->conditionalValues;
    }

    public function withValueType(ParameterValueType $valueType): self
    {
        return new self(
            name: $this->name,
            description: $this->description,
            defaultValue: $this->defaultValue,
            conditionalValues: $this->conditionalValues,
            valueType: $valueType,
        );
    }

    public function valueType(): ParameterValueType
    {
        return $this->valueType;
    }

    /**
     * @return RemoteConfigParameterShape
     */
    public function toArray(): array
    {
        $conditionalValues = [];

        foreach ($this->conditionalValues() as $conditionalValue) {
            $conditionalValues[$conditionalValue->conditionName()] = $conditionalValue->toArray();
        }

        $array = [];

        if ($this->defaultValue instanceof ParameterValue) {
            $array['defaultValue'] = $this->defaultValue->toArray();
        }

        if ($conditionalValues !== []) {
            $array['conditionalValues'] = $conditionalValues;
        }

        if ($this->description !== '') {
            $array['description'] = $this->description;
        }

        $array['valueType'] = $this->valueType->value;

        return $array;
    }

    /**
     * @return RemoteConfigParameterShape
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
