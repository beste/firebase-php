<?php

declare(strict_types=1);

namespace Kreait\Firebase\RemoteConfig;

use JsonSerializable;

use function array_key_exists;

/**
 * @phpstan-import-type RemoteConfigExperimentValueShape from ExperimentValue
 * @phpstan-import-type RemoteConfigPersonalizationValueShape from PersonalizationValue
 * @phpstan-import-type RemoteConfigRolloutValueShape from RolloutValue
 *
 * @phpstan-type RemoteConfigParameterValueShape array{
 *     value?: string,
 *     useInAppDefault?: bool,
 *     personalizationValue?: RemoteConfigPersonalizationValueShape,
 *     rolloutValue?: RemoteConfigRolloutValueShape,
 *     experimentValue?: RemoteConfigExperimentValueShape
 * }
 *
 * @see https://firebase.google.com/docs/reference/remote-config/rest/v1/RemoteConfig#remoteconfigparametervalue
 */
final readonly class ParameterValue implements JsonSerializable
{
    private function __construct(
        private ?string $value = null,
        private ?bool $useInAppDefault = null,
        private ?PersonalizationValue $personalizationValue = null,
        private ?RolloutValue $rolloutValue = null,
        private ?ExperimentValue $experimentValue = null,
    ) {
    }

    public static function withValue(string $value): self
    {
        return new self(value: $value);
    }

    public static function inAppDefault(): self
    {
        return new self(useInAppDefault: true);
    }

    public static function withPersonalizationValue(PersonalizationValue $value): self
    {
        return new self(personalizationValue: $value);
    }

    public static function withRolloutValue(RolloutValue $value): self
    {
        return new self(rolloutValue: $value);
    }

    public static function withExperimentValue(ExperimentValue $value): self
    {
        return new self(experimentValue: $value);
    }

    /**
     * @param RemoteConfigParameterValueShape $data
     */
    public static function fromArray(array $data): self
    {
        if (array_key_exists('value', $data)) {
            return self::withValue($data['value']);
        }

        if (array_key_exists('useInAppDefault', $data)) {
            return self::inAppDefault();
        }

        if (array_key_exists('personalizationValue', $data)) {
            return self::withPersonalizationValue(PersonalizationValue::fromArray($data['personalizationValue']));
        }

        if (array_key_exists('rolloutValue', $data)) {
            return self::withRolloutValue(RolloutValue::fromArray($data['rolloutValue']));
        }

        if (array_key_exists('experimentValue', $data)) {
            return self::withExperimentValue(ExperimentValue::fromArray($data['experimentValue']));
        }

        return new self();
    }

    /**
     * @return RemoteConfigParameterValueShape
     */
    public function toArray(): array
    {
        if ($this->value !== null) {
            return ['value' => $this->value];
        }

        if ($this->useInAppDefault !== null) {
            return ['useInAppDefault' => $this->useInAppDefault];
        }

        if ($this->personalizationValue instanceof PersonalizationValue) {
            return ['personalizationValue' => $this->personalizationValue->toArray()];
        }

        if ($this->rolloutValue instanceof RolloutValue) {
            return ['rolloutValue' => $this->rolloutValue->toArray()];
        }

        if ($this->experimentValue instanceof ExperimentValue) {
            return ['experimentValue' => $this->experimentValue->toArray()];
        }

        return [];
    }

    /**
     * @return RemoteConfigParameterValueShape
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
